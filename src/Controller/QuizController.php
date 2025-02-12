<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Resultat;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz')]
final class QuizController extends AbstractController
{
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/quiz/{id}/start', name: 'app_quiz_start')]
    public function start(
        Quiz $quiz, 
        Request $request, 
        EntityManagerInterface $em, 
        ReponseRepository $reponseRepository
    ): Response {
        // Récupérer l'apprenant (utilisateur authentifié)
        $apprenant = $this->getUser(); // Assurez-vous que l'apprenant est un utilisateur authentifié
        
        if ($request->isMethod('POST')) {
            $totalReponsesCorrectes = 0;
            $totalReponses = 0;
    
            foreach ($quiz->getQuestions() as $question) {
                $reponsesUtilisateur = $request->request->all("question_{$question->getId()}");
    
                // Récupérer toutes les bonnes réponses de cette question
                $bonnesReponses = $question->getReponses()->filter(fn($r) => $r->isEstCorrecte());
    
                // Comptage des réponses correctes pour le calcul du score
                $totalReponses += count($bonnesReponses); // Nombre total de bonnes réponses attendues
    
                foreach ($bonnesReponses as $bonneReponse) {
                    if (in_array($bonneReponse->getId(), $reponsesUtilisateur ?? [])) {
                        $totalReponsesCorrectes++;
                    }
                }
            }
    
            // Calcul du pourcentage de réussite
            $score = $totalReponses > 0 ? ($totalReponsesCorrectes / $totalReponses) * 100 : 0;
    
            // Sauvegarde du résultat
            $resultat = new Resultat();
            $resultat->setQuiz($quiz);
            $resultat->setScore(round($score, 2)); // Arrondi à 2 décimales
            $resultat->setApprenant($apprenant); // Associer l'apprenant au résultat
    
            $em->persist($resultat);
            $em->flush();
    
            return $this->redirectToRoute('quiz_result', ['id' => $resultat->getId()]);
        }
    
        return $this->render('quiz/start.html.twig', ['quiz' => $quiz]);
    }

    #[Route('/quiz/result/{id}', name: 'quiz_result')]
    public function result(Resultat $resultat): Response
    {
        return $this->render('quiz/result.html.twig', ['resultat' => $resultat]);
    }

    #[Route('/quiz/{id}/stats', name: 'app_quiz_stats')]
    public function stats(Quiz $quiz, EntityManagerInterface $em): Response {
        // Convertir la Collection en tableau
        $resultats = $quiz->getResultats()->toArray();

        // Calculer le total des résultats
        $total = count($resultats);

        // Calculer la moyenne des scores
        $moyenne = $total > 0 ? array_sum(array_map(fn($r) => $r->getScore(), $resultats)) / $total : 0;

        // Trouver le meilleur score
        $meilleur = $total > 0 ? max(array_map(fn($r) => $r->getScore(), $resultats)) : 0;

        return $this->render('quiz/stats.html.twig', [
            'quiz' => $quiz,
            'moyenne' => $moyenne,
            'meilleur' => $meilleur,
            'tentatives' => $total
        ]);
    }
}
