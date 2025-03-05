<?php

namespace App\EventListener;

use App\Service\UserLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;


class LogoutListener implements EventSubscriberInterface
{
    private UserLogger $userLogger;

    public function __construct(UserLogger $userLogger)
    {
        $this->userLogger = $userLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $request = $event->getRequest();

        // Enregistrer le log de déconnexion
        $this->userLogger->log(
            $user,
            'logout',
            sprintf('IP: %s', $request->getClientIp())
        );
    }
}