<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use App\Entity\User;

class LoginListener implements EventSubscriberInterface
{
    private $entityManager;
    private $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            // Mettre à jour la dernière connexion
            $user->setLastConnexion(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Rediriger l'utilisateur en fonction de son rôle
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $response = new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
                $event->getRequest()->getSession()->set('_security.main.target_path', $response->getTargetUrl());
            }
        }
    }
}