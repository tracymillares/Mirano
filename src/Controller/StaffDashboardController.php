<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Service\ActivityLogger;

#[Route('/staff')]
class StaffDashboardController extends AbstractController
{
    private ActivityLogger $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    #[Route('/dashboard', name: 'app_staff_dashboard')]
    public function index(
        OrderRepository $orderRepository,
        ReservationRepository $reservationRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $user = $this->getUser();

        return $this->render('staff_dashboard/index.html.twig', [
            'totalOrders' => $orderRepository->count(['createdBy' => $user]),
            'totalReservations' => $reservationRepository->count(['createdBy' => $user]),
        ]);
    }

    #[Route('/profile', name: 'app_staff_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $user = $this->getUser();

        return $this->render('staff_dashboard/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/change-password', name: 'staff_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User || !$user instanceof PasswordAuthenticatedUserInterface) {
            throw $this->createAccessDeniedException('Invalid user.');
        }

        $form = $this->createFormBuilder()
            ->add('current_password', PasswordType::class, [
                'label' => 'Current Password',
                'required' => true,
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Repeat New Password'],
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$passwordHasher->isPasswordValid($user, $data['current_password'])) {
                $this->addFlash('error', 'Current password is incorrect.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword($user, $data['new_password']);
                $user->setPassword($hashedPassword);
                $em->flush();

                // Log password change
                $this->activityLogger->log(
                    'CHANGE_PASSWORD',
                    'User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ') changed their password'
                );

                $this->addFlash('success', 'Password successfully changed.');
                return $this->redirectToRoute('app_staff_profile');
            }
        }

        return $this->render('password_change/staff_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
