<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\MenuRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository, 
        MenuRepository $menuRepository, 
        ReservationRepository $reservationRepository
    ): Response
    {
        // Count total users and staff
        $totalUsers = $userRepository->count([]);
        $totalStaff = count($userRepository->findByRole('ROLE_STAFF'));

        // Count total menu items
        $totalMenuItems = $menuRepository->count([]);

        // Count total reservations
        $totalReservations = $reservationRepository->count([]);

        return $this->render('admin_dashboard/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalStaff' => $totalStaff,
            'totalMenuItems' => $totalMenuItems,
            'totalReservations' => $totalReservations,
        ]);

        
    }
    
}
