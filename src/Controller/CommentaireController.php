<?php
namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Event;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ProfanityFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaire')]
final class CommentaireController extends AbstractController
{
    private ProfanityFilterService $profanityFilterService;

    public function __construct(ProfanityFilterService $profanityFilterService)
    {
        $this->profanityFilterService = $profanityFilterService;
    }

    #[Route('/event/{id}', name: 'app_event_show', methods: ['GET', 'POST'])]
    public function show(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $commentaire->setEvenement($event);

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $censoredTitle = $this->profanityFilterService->censorText($commentaire->getTitre());
            $censoredContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setTitre($censoredTitle);
            $commentaire->setContenu($censoredContent);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'commentaireForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $censoredTitle = $this->profanityFilterService->censorText($commentaire->getTitre());
            $censoredContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setTitre($censoredTitle);
            $commentaire->setContenu($censoredContent);

            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->get('csrf_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/commentaire', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        // Note: Censoring here only affects display, not persisted data
        foreach ($commentaires as $commentaire) {
            $commentaire->setTitre($this->profanityFilterService->censorText($commentaire->getTitre()));
            $commentaire->setContenu($this->profanityFilterService->censorText($commentaire->getContenu()));
        }

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }
}