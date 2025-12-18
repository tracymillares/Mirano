<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaffMenuController extends AbstractController
{
    #[Route('/staff/menu', name: 'staff_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('staff_records/menu.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }
}
