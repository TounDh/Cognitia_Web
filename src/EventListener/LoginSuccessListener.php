<?php
// src/EventListener/LoginSuccessListener.php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginSuccessListener implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        // Vérifiez si l'utilisateur a le rôle ROLE_ADMIN
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            $event->getRequest()->getSession()->set('_security.main.target_path', $this->urlGenerator->generate('app_dashboard'));
        }
    }
}