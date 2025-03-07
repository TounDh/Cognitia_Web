<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private RouterInterface $router;
    private FlashBagInterface $flashBag;

    public function __construct(RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->router = $router;
        $this->flashBag = $flashBag;
    }
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.'
            ], Response::HTTP_FORBIDDEN);
        }

        $this->flashBag->add('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');

        return new RedirectResponse($this->router->generate('app_home')); // Redirige vers la page d'accueil
    }
} 