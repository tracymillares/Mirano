<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StaffDashboardController extends AbstractController
{
    #[Route('/staff/dashboard', name: 'app_staff_dashboard')]
    public function index(): Response
    {
        return $this->render('staff_dashboard/index.html.twig', [
            'controller_name' => 'StaffDashboardController',
        ]);
    }
}
