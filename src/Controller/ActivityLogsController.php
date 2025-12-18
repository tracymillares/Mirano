<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/activity-logs')]
class ActivityLogsController extends AbstractController
{
    #[Route('/', name: 'admin_activity_logs_index', methods: ['GET'])]
    public function index(ActivityLogRepository $activityLogRepository): Response
    {
        // Ensure only admins can view activity logs
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Fetch all logs ordered by latest first
        $logs = $activityLogRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('activity_logs/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}
