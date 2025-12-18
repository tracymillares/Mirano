<?php

namespace App\EventSubscriber;

use App\Service\ActivityLogger;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Bundle\SecurityBundle\Security;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ActivityLogger $activityLogger,
        private Security $security
    ) {}

    /**
     * Handles successful login events.
     */
    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $this->activityLogger->log(
                'LOGIN',
                'User: ' . $user->getUserIdentifier() . ' (ID: ' . $user->getId() . ') logged in'
            );
        }
    }

    /**
     * Handles logout events.
     */
    public function onLogout(LogoutEvent $event): void
    {
        // Security may still return null if no user is authenticated
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $this->activityLogger->log(
                'LOGOUT',
                'User: ' . $user->getUserIdentifier() . ' (ID: ' . $user->getId() . ') logged out'
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onLogin',
            LogoutEvent::class => 'onLogout',
        ];
    }
}
