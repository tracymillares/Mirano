<?php

namespace App\Service;

use App\Entity\ActivityLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ActivityLogger
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function log(string $action, ?string $targetData = null): void
    {
        $user = $this->security->getUser();

        // ✅ Only log authenticated users
        if (!$user instanceof User) {
            return;
        }

        $log = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUserIdentifier());
        $log->setRole($user->getRoles()[0] ?? 'ROLE_USER');
        $log->setAction($action);
        $log->setTargetData($targetData ?? 'N/A');

        $this->em->persist($log);
        $this->em->flush();
    }
}
