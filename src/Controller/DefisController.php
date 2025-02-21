<?php

namespace App\Controller;

use App\Entity\Defis;
use App\Form\DefisType;
use App\Repository\DefisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/defis')]
final class DefisController extends AbstractController
{
    #[Route(name: 'app_defis_index', methods: ['GET'])]
    public function index(DefisRepository $defisRepository): Response
    {
        return $this->render('defis/index.html.twig', [
            'defis' => $defisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_defis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $defi = new Defis();
        $form = $this->createForm(DefisType::class, $defi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($defi);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('defis/new.html.twig', [
            'defi' => $defi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_defis_show', methods: ['GET'])]
    public function show(Defis $defi): Response
    {
        return $this->render('defis/show.html.twig', [
            'defi' => $defi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_defis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Defis $defi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DefisType::class, $defi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('defis/edit.html.twig', [
            'defi' => $defi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_defis_delete', methods: ['POST'])]
    public function delete(Request $request, Defis $defi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$defi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($defi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
