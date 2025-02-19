<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/question')]
#[IsGranted('ROLE_INSTRUCTEUR')]

final class QuestionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{quiz_id<\d+>}', name: 'app_question_index', methods: ['GET'])]
    public function index(int $quiz_id, QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le quiz à partir de l'ID passé
        $quiz = $entityManager->getRepository(Quiz::class)->find($quiz_id);
        
        // Vérifier si le quiz existe
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Récupérer toutes les questions associées au quiz
        $questions = $questionRepository->findBy(['quiz' => $quiz]);

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
            'quiz' => $quiz, // Passer l'objet quiz à la vue
        ]);
    }

    #[Route('/{quiz_id}/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(int $quiz_id, Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository, QuestionRepository $questionRepository): Response
    {
        // Récupérer le quiz par son ID
        $quiz = $quizRepository->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz spécifié n\'existe pas.');
        }

        // Créer une nouvelle question
        $question = new Question();
        $question->setQuiz($quiz); // Assurez-vous que la relation est correctement définie dans votre entité Question

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            // Rediriger vers la page de liste des questions pour ce quiz
            return $this->redirectToRoute('app_question_index', ['quiz_id' => $quiz->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz, // Passer le quiz à la vue
        ]);
    }


    #[Route('/{quiz_id<\d+>}/question/{id<\d+>}', name: 'app_question_show', methods: ['GET'])]
    public function show(int $quiz_id, Question $question): Response
    {
        // Utiliser l'injection de l'EntityManager
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Vérifier que la question appartient au quiz
        if ($question->getQuiz()->getId() !== $quiz_id) {
            throw $this->createNotFoundException('Question non trouvée pour ce quiz');
        }

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'quiz' => $quiz,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, QuestionRepository $questionRepository, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {
        // Récupérer la question à éditer
        $question = $questionRepository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('La question spécifiée n\'existe pas.');
        }

        // Récupérer le quiz associé à la question
        $quiz = $question->getQuiz();

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz associé à cette question n\'existe pas.');
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_index', ['quiz_id' => $quiz->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
            'quiz' => $quiz, // Passer le quiz à la vue
        ]);
    }


    #[Route('/{id}/delete', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $quizId = $question->getQuiz()->getId();  // Récupérer l'ID du quiz associé à la question

        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        // Rediriger vers la liste des questions du quiz, en passant quiz_id
        return $this->redirectToRoute('app_question_index', ['quiz_id' => $quizId], Response::HTTP_SEE_OTHER);
    }

    /** Fonctionnalité pour l'admin (dashboard) */

    #[Route('/dashboard/{quiz_id<\d+>}', name: 'dashboard_question_index', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function dashboardindex(int $quiz_id, QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le quiz à partir de l'ID passé
        $quiz = $entityManager->getRepository(Quiz::class)->find($quiz_id);
        
        // Vérifier si le quiz existe
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Récupérer toutes les questions associées au quiz
        $questions = $questionRepository->findBy(['quiz' => $quiz]);

        return $this->render('dashboard/question/question.html.twig', [
            'questions' => $questions,
            'quiz' => $quiz, // Passer l'objet quiz à la vue
        ]);
    }

    #[Route('/dashboard/{quiz_id}/new', name: 'dashboard_question_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function dashboardnew(int $quiz_id, Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository, QuestionRepository $questionRepository): Response
    {
        // Récupérer le quiz par son ID
        $quiz = $quizRepository->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz spécifié n\'existe pas.');
        }

        // Créer une nouvelle question
        $question = new Question();
        $question->setQuiz($quiz); // Assurez-vous que la relation est correctement définie dans votre entité Question

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            // Rediriger vers la page de liste des questions pour ce quiz
            return $this->redirectToRoute('dashboard_question_index', ['quiz_id' => $quiz->getId()]);
        }

        return $this->render('dashboard/question/ajoutQuestion.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz, // Passer le quiz à la vue
        ]);
    }

    #[Route('/dashboard/{quiz_id<\d+>}/question/{id<\d+>}', name: 'dashboard_question_show', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function dashboardshow(int $quiz_id, Question $question): Response
    {
        // Utiliser l'injection de l'EntityManager
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($quiz_id);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Vérifier que la question appartient au quiz
        if ($question->getQuiz()->getId() !== $quiz_id) {
            throw $this->createNotFoundException('Question non trouvée pour ce quiz');
        }

        return $this->render('dashboard/question/showQuestion.html.twig', [
            'question' => $question,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/dashboard/{id}/edit', name: 'dashboard_question_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function dashboardedit(int $id, Request $request, QuestionRepository $questionRepository, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
    {
        // Récupérer la question à éditer
        $question = $questionRepository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('La question spécifiée n\'existe pas.');
        }

        // Récupérer le quiz associé à la question
        $quiz = $question->getQuiz();

        if (!$quiz) {
            throw $this->createNotFoundException('Le quiz associé à cette question n\'existe pas.');
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_question_index', ['quiz_id' => $quiz->getId()]);
        }

        return $this->render('dashboard/question/modifQuestion.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
            'quiz' => $quiz, // Passer le quiz à la vue
        ]);
    }

    #[Route('/dashboard/{id}/delete', name: 'dashboard_question_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function dashboarddelete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $quizId = $question->getQuiz()->getId();  // Récupérer l'ID du quiz associé à la question

        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        // Rediriger vers la liste des questions du quiz, en passant quiz_id
        return $this->redirectToRoute('dashboard_question_index', ['quiz_id' => $quizId], Response::HTTP_SEE_OTHER);
    }
}
