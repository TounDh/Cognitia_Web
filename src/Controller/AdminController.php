<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; 


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
		$this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

		/** @var User $user */
		$user = $this->getUser();

		return match ($user->isVerified()) {
			true => $this->render("dashboard/index.html.twig"),
			false => $this->render("admin/please-verify-email.html.twig"),
		};
    }


	#[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
