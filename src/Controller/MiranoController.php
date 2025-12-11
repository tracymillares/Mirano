<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MiranoController extends AbstractController
{
    #[Route('/mirano', name: 'app_mirano')]
    public function index(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();

        return $this->render('mirano/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/menu', name: 'app_full_menu')]
    public function fullMenu(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();

        return $this->render('mirano/full_menu.html.twig', [
            'menus' => $menus,
        ]);
    }
}
