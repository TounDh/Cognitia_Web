<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{question_id<\d+>}', name: 'app_reponse_index', methods: ['GET'])]
    public function index(int $question_id, ReponseRepository $reponseRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le question à partir de l'ID passé
        $question = $entityManager->getRepository(Question::class)->find($question_id);
        
        // Vérifier si le quiz existe
        if (!$question) {
            throw $this->createNotFoundException('Question non trouvé');
        }

        // Récupérer toutes les questions associées au quiz
        $reponses = $reponseRepository->findBy(['question' => $question]);

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
            'question' => $question, // Passer l'objet question à la vue
        ]);
    }

    #[Route('/{question_id}/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(int $question_id, Request $request, EntityManagerInterface $entityManager, QuestionRepository $questionRepository, ReponseRepository $reponseRepository): Response
    {
        // Récupérer le quiz par son ID
        $question = $questionRepository->find($question_id);

        if (!$question) {
            throw $this->createNotFoundException('Le question spécifié n\'existe pas.');
        }

        // Créer une nouvelle reponse
        $reponse = new Reponse();
        $reponse->setQuestion($question); // Assurez-vous que la relation est correctement définie dans votre entité Reponse

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Rediriger vers la page de liste des reponses pour ce question
            return $this->redirectToRoute('app_reponse_index', ['question_id' => $question->getId()]);
        }

        return $this->render('reponse/new.html.twig', [
            'form' => $form->createView(),
            'question' => $question, // Passer le question à la vue
        ]);
    }

    #[Route('/{question_id<\d+>}/reponse/{id<\d+>}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(int $question_id, Reponse $reponse): Response
    {
        // Utiliser l'injection de l'EntityManager
        $question = $this->entityManager->getRepository(Question::class)->find($question_id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvé');
        }

        // Vérifier que la reponse appartient au question
        if ($reponse->getQuestion()->getId() !== $question_id) {
            throw $this->createNotFoundException('Reponse non trouvée pour ce question');
        }

        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
            'question' => $question,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ReponseRepository $reponseRepository, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
    {
        // Récupérer la reponse à éditer
        $reponse = $reponseRepository->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('La reponse spécifiée n\'existe pas.');
        }

        // Récupérer le question associé à la question
        $question = $reponse->getQuestion();

        if (!$question) {
            throw $this->createNotFoundException('Le question associé à cette question n\'existe pas.');
        }

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', ['question_id' => $question->getId()]);
        }

        return $this->render('reponse/edit.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse,
            'question' => $question, // Passer le question à la vue
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $questionId = $reponse->getQuestion()->getId();  // Récupérer l'ID du question associé à la reponse

        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', ['question_id' => $questionId], Response::HTTP_SEE_OTHER);
    }
}
