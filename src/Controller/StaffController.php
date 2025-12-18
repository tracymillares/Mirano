<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\StaffManagementType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ActivityLogger;

#[Route('/admin/staff')]
class StaffController extends AbstractController
{
    private ActivityLogger $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    // ✅ List all users
    #[Route('/', name: 'staff_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('staff/index.html.twig', [
            'users' => $users,
        ]);
    }

    // ✅ Add new staff
    #[Route('/new', name: 'staff_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(StaffManagementType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            // Log creation
            $this->activityLogger->log(
                'CREATE',
                'User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ')'
            );

            return $this->redirectToRoute('staff_index');
        }

        return $this->render('staff/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ✅ Edit user
    #[Route('/{id}/edit', name: 'staff_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(StaffManagementType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $em->flush();

            // Log update
            $this->activityLogger->log(
                'UPDATE',
                'User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ')'
            );

            return $this->redirectToRoute('staff_index');
        }

        return $this->render('staff/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    // ✅ Delete user
    #[Route('/{id}/delete', name: 'staff_delete', methods:['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            // Log deletion
            $this->activityLogger->log(
                'DELETE',
                'User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ')'
            );
        }

        return $this->redirectToRoute('staff_index');
    }

    // ✅ Toggle staff status
    #[Route('/staff/{id}/toggle-status', name: 'staff_toggle_status', methods: ['POST'])]
    public function toggleStatus(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('toggle_status_'.$user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user->setIsActive(!$user->getIsActive());
        $em->flush();

        // Log status change
        $this->activityLogger->log(
            'UPDATE_STATUS',
            'User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ') - Status: ' . ($user->getIsActive() ? 'Enabled' : 'Disabled')
        );

        $this->addFlash(
            'success',
            $user->getIsActive()
                ? 'Staff account enabled.'
                : 'Staff account disabled.'
        );

        return $this->redirectToRoute('staff_index');
    }
}
