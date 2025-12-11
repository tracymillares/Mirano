<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/staff')]
class StaffController extends AbstractController
{
    #[Route('/', name: 'app_admin_staff_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/staff/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'app_admin_staff_create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('app_admin_staff_index');
        }

        return $this->render('admin/staff/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_staff_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Optional: reset password if a new one is provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }

            $em->flush();
            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('app_admin_staff_index');
        }

        return $this->render('admin/staff/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_staff_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_staff_index');
    }
}
