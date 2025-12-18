<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMenuController extends AbstractController
{
    #[Route('/admin/menu', name: 'admin_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('admin_records/menu.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }
}