<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class AdminAccessListener
{
    private Security $security;
    private RouterInterface $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            $user = $this->security->getUser();

            if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
                $event->setResponse(new RedirectResponse($this->router->generate('app_home')));
            }
        }
    }
}
