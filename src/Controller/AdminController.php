<?php

namespace App\Controller;

use App\Form\PasswordChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ActivityLogger;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/profile', name: 'app_admin_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('admin/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/change-password', name: 'app_admin_change_password')]
    public function changePassword(
    Request $request,
    UserPasswordHasherInterface $hasher,
    EntityManagerInterface $em,
    ActivityLogger $activityLogger
): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // runtime safety check
        if (!$user || !($user instanceof \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface)) {
            throw $this->createAccessDeniedException('Invalid user.');
        }

        $form = $this->createForm(PasswordChangeType::class);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $user->setPassword(
        $hasher->hashPassword($user, $form->get('plainPassword')->getData())
    );
    $em->flush();

    $this->addFlash('success', 'Password updated successfully!');
    return $this->redirectToRoute('app_admin_profile');
}


        return $this->render('password_change/change_password.html.twig', [
            'passwordForm' => $form->createView()
        ]);
    }
}
