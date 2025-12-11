<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(MenuRepository $menuRepo, ReservationRepository $reservationRepo): Response
    {
        $menuCount = $menuRepo->count([]);
        $reservationCount = $reservationRepo->count([]);

        return $this->render('admin_dashboard/index.html.twig', [
            'menuCount' => $menuCount,
            'reservationCount' => $reservationCount,
        ]);
    }
}
