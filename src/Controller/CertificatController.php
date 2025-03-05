<?php

namespace App\Controller;

use App\Entity\Certificat;
use App\Form\CertificatType;
use App\Repository\CertificatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/certificat')]
final class CertificatController extends AbstractController
{
    #[Route(name: 'app_certificat_index', methods: ['GET'])]
    public function index(CertificatRepository $certificatRepository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $roles = $user->getRoles(); // Récupérer les rôles de l'utilisateur
    
        if (in_array('ROLE_APPRENANT', $roles)) {
            // Si c'est un apprenant, afficher seulement ses certificats
            $certificats = $certificatRepository->findBy(['apprenant' => $user]);
        } else {
            // Sinon (admin ou instructeur), afficher tous les certificats
            $certificats = $certificatRepository->findAll();
        }
        return $this->render('certificat/index.html.twig', [
            'certificats' => $certificats,
        ]);
    }
    

    #[Route('/new', name: 'app_certificat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $certificat = new Certificat();
        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($certificat);
            $entityManager->flush();

            return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('certificat/new.html.twig', [
            'certificat' => $certificat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_certificat_show', methods: ['GET'])]
    public function show(Certificat $certificat): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('certificat/show.html.twig', [
            'certificat' => $certificat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_certificat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Certificat $certificat, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('certificat/edit.html.twig', [
            'certificat' => $certificat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_certificat_delete', methods: ['POST'])]
    public function delete(Request $request, Certificat $certificat, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete'.$certificat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($certificat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
    }


    /** Fonctionnalite pour l'admin */

    #[Route('/dashboard/certificat', name: 'dashboard_certificat')]
    public function dashboardCertificat(CertificatRepository $certificatRepository, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $query = $request->query->get('q');
        
        // Créer le query builder avec la recherche si elle existe
        $queryBuilder = $certificatRepository->createQueryBuilder('c')
            ->leftJoin('c.quiz', 'q')
            ->leftJoin('c.apprenant', 'a');
            
        if ($query) {
            $queryBuilder
                ->where('q.titre LIKE :query')
                ->orWhere('a.firstName LIKE :query')
                ->orWhere('a.lastName LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }
        
        // Paginer les résultats
        $paginator = $certificatRepository->paginateQuery($queryBuilder, $page, $limit);

        return $this->render('dashboard/certificat/certificat.html.twig', [
            'certificats' => $paginator,
            'currentPage' => $page,
            'totalPages' => ceil($paginator->count() / $limit),
            'limit' => $limit,
            'totalItems' => $paginator->count()
        ]);
    }

    #[Route('/dashboard/certificat/{id}', name: 'dashboard_certificat_delete', methods: ['POST'])]
    public function deleteCertificat(Request $request, Certificat $certificat, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete'.$certificat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($certificat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_certificat', [], Response::HTTP_SEE_OTHER);
    }
}
