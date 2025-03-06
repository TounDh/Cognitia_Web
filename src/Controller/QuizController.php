<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Resultat;
use App\Entity\Certificat;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\ReponseRepository;
use App\Repository\CertificatRepository;
use App\Repository\UserRepository;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use TCPDF;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Cours;


#[Route('/quiz')]

final class QuizController extends AbstractController
{
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    { 
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $roles = $user->getRoles(); // Récupérer les rôles de l'utilisateur
    
        if (in_array('ROLE_INSTRUCTEUR', $roles)) {
            // Si c'est un instructeur, afficher seulement ses quiz
            $quizzes = $quizRepository->findBy(['instructeur' => $user]);
        } else {
            // Sinon (admin ou apprenant), afficher tous les quiz
            $quizzes = $quizRepository->findAll();
        }
    
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/new/{id}', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Cours $cours): Response
    {
        if (!$this->isGranted('ROLE_INSTRUCTEUR')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifier si le cours a déjà un quiz
        if ($cours->getQuiz() !== null) {
            $this->addFlash('error', 'Ce cours a déjà un quiz associé.');
            return $this->redirectToRoute('app_cours_show', ['id' => $cours->getId()]);
        }

        $quiz = new Quiz();
        $quiz->setCours($cours);
        $quiz->setInstructeur($this->getUser());

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash('success', 'Quiz créé avec succès!');
            return $this->redirectToRoute('app_cours_show', ['id' => $cours->getId()]);
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
            'cours' => $cours
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(int $id, QuizRepository $quizRepository): Response
    {
        $quiz = $quizRepository->find($id);
        
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        if (!$this->isGranted('ROLE_INSTRUCTEUR')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_INSTRUCTEUR')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Quiz modifié avec succès!');
            return $this->redirectToRoute('app_quiz_show', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/startSession', name: 'app_quiz_start_session')]
    public function startSession(Quiz $quiz): Response
    {
        if (!$this->isGranted('ROLE_APPRENANT')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('quiz/startSession.html.twig', ['quiz' => $quiz]);
    }

    #[Route('/{id}/start', name: 'app_quiz_start')]
    public function start(
        Quiz $quiz, 
        Request $request, 
        EntityManagerInterface $em, 
        ReponseRepository $reponseRepository
    ): Response {

        if (!$this->isGranted('ROLE_APPRENANT')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

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

            if ($score >= 80) {
                // Créer un nouveau certificat
                $certificat = new Certificat();
                $certificat->setApprenant($apprenant);
                $certificat->setQuiz($quiz);
                $certificat->setScore($score);
                $certificat->setDateObtention(new \DateTimeImmutable());
                $em->persist($certificat);
                $em->flush();
            }
            
            return $this->redirectToRoute('quiz_result', ['id' => $resultat->getId()]);
        }
    
        return $this->render('quiz/start.html.twig', ['quiz' => $quiz]);
    }

    #[Route('/result/{id}', name: 'quiz_result')]
    public function result(Resultat $resultat): Response
    {
        return $this->render('quiz/result.html.twig', ['resultat' => $resultat]);
    }

    #[Route('/{id}/stats', name: 'app_quiz_stats')]
    public function stats(Quiz $quiz, EntityManagerInterface $em): Response {
        if (!$this->isGranted('ROLE_INSTRUCTEUR')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        // Convertir la Collection en tableau
        $resultats = $quiz->getResultats()->toArray();

        // Calculer le total des résultats
        $total = count($resultats);

        // Calculer la moyenne des scores
        $moyenne = $total > 0 ? array_sum(array_map(fn($r) => $r->getScore(), $resultats)) / $total : 0;

        // Trouver le meilleur score
        $meilleur = $total > 0 ? max(array_map(fn($r) => $r->getScore(), $resultats)) : 0;

        // Calcul de la répartition des scores
        $repartition = array_fill(0, 10, 0);
        foreach ($resultats as $resultat) {
            $index = min((int)($resultat->getScore() / 10), 9);
            $repartition[$index]++;
        }

        return $this->render('quiz/stats.html.twig', [
            'quiz' => $quiz,
            'moyenne' => $moyenne,
            'meilleur' => $meilleur,
            'tentatives' => $total,
            'repartition' => $repartition
        ]);
    }

    #[Route('/certificate/{id}', name: 'app_generate_certificate')]
    public function generateCertificate(
        int $id,
        CertificatRepository $certificatRepository,  
        EntityManagerInterface $entityManager
    ): Response {
        $certificat = $certificatRepository->find($id);
        
        if (!$certificat) {
            throw $this->createNotFoundException('Certificat non trouvé');
        }
    
        $quiz = $certificat->getQuiz();
        $user = $certificat->getApprenant();
        $score = $certificat->getScore();
        $responsable = $quiz->getInstructeur();
    
        // Création du PDF
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('E-Learning Platform');
        $pdf->SetAuthor('Cognitia');
        $pdf->SetTitle('Certificat de réussite - ' . $quiz->getTitre());
    
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Ajout du filigrane (Avant d'écrire du texte)
        $logoPath = $this->getParameter('kernel.project_dir') . '/public/cognitia.png';
        // $pdf->Image($logoPath, 60, 60, 180, 120, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);

        // Ajout d'une bordure élégante
        $pdf->SetDrawColor(0, 51, 102);
        $pdf->SetLineWidth(3);
        $pdf->Rect(10, 10, $pdf->GetPageWidth()-20, $pdf->GetPageHeight()-20);
    
        // Ajout du logo officiel en haut
        $pdf->Image($logoPath, 20, 20, 40, 40, 'PNG');

        // Ajout du titre
        $pdf->SetFont('helvetica', 'B', 40);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Ln(10);
        $pdf->Cell(0, 20, 'Certificat de Réussite', 0, 1, 'C');
    
        // Ligne de séparation
        $pdf->SetDrawColor(0, 51, 102);
        $pdf->SetLineWidth(1);
        $pdf->Line(50, 70, 250, 70);
    
        // Texte d'introduction
        $pdf->SetFont('helvetica', '', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(15);
        $pdf->Cell(0, 10, 'Ce certificat est décerné à', 0, 1, 'C');
    
        // Nom de l'apprenant avec police stylée
        $pdf->SetFont('times', 'B', 30);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, strtoupper($user->getFirstName() . ' ' . $user->getLastName()), 0, 1, 'C');
    
        // Description du quiz
        $pdf->SetFont('helvetica', '', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'pour avoir complété avec succès le quiz', 0, 1, 'C');
    
        $pdf->SetFont('helvetica', 'B', 22);
        $pdf->SetTextColor(13, 110, 253);
        $pdf->Cell(0, 10, '"' . $quiz->getTitre() . '"', 0, 1, 'C');
    
        // Score et date
        $pdf->SetFont('helvetica', '', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'avec un score de ' . $score . '%', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Date : ' . (new \DateTime())->format('d/m/Y'), 0, 1, 'C');
    
        // Signature et responsable
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', 'I', 16);
        $pdf->Cell(0, 10, '__________________________', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Signature du responsable', 0, 1, 'C');

        // Nom du responsable sous la signature
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, strtoupper($responsable->getFirstName() . ' ' . $responsable->getLastName()), 0, 1, 'C');
    
        // Génération du nom du fichier
        $filename = sprintf('certificat_%s_%s.pdf', 
            preg_replace('/[^A-Za-z0-9]/', '_', $user->getFirstName(). '_' .$user->getLastName()),
            preg_replace('/[^A-Za-z0-9]/', '_', $quiz->getTitre())
        );
    
        return new Response(
            $pdf->Output($filename, 'D'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename)
            ]
        );
    }




    /** Fonctionnalité pour l'admin (dashboard) */

    #[Route('/dashboard/quiz', name: 'dashboard_quiz')]
    public function dashboardQuiz(
        QuizRepository $quizRepository, 
        UserRepository $userRepository,
        CoursRepository $coursRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $query = $request->query->get('q');
        $instructeurId = $request->query->getInt('instructeur');
        $coursId = $request->query->getInt('cours');

        // Créer le query builder avec la recherche si elle existe
        $queryBuilder = $quizRepository->createQueryBuilder('q')
            ->leftJoin('q.cours', 'a')
            ->leftJoin('q.instructeur', 'i');
            
        if ($query) {
            $queryBuilder
                ->andWhere('q.titre LIKE :query')
                ->orWhere('a.titre LIKE :query')
                ->orWhere('i.firstName LIKE :query')
                ->orWhere('i.lastName LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        // Ajout des filtres
        if ($instructeurId) {
            $queryBuilder
                ->andWhere('i.id = :instructeurId')
                ->setParameter('instructeurId', $instructeurId);
        }

        if ($coursId) {
            $queryBuilder
                ->andWhere('a.id = :coursId')
                ->setParameter('coursId', $coursId);
        }

        // Paginer les résultats
        $quizzes = $paginator->paginate(
            $queryBuilder->getQuery(), // La requête à paginer
            $page,                    // Numéro de page
            $limit                    // Nombre d'éléments par page
        );

        return $this->render('dashboard/quiz/quiz.html.twig', [
            'quizzes' => $quizzes,
            'currentPage' => $page,
            'totalPages' => ceil($quizzes->getTotalItemCount() / $limit),
            'limit' => $limit,
            'totalItems' => $quizzes->getTotalItemCount(),
            'instructeurs' => $userRepository->findByRole('ROLE_INSTRUCTEUR'),
            'coursList' => $coursRepository->findAll()
        ]);
    }

    #[Route('/dashboard/newQuiz', name: 'dashboard_quiz_new', methods: ['GET', 'POST'])]
    public function dashboardnew(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_quiz', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/quiz/ajoutQuiz.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/{id}/edit', name: 'dashboard_quiz_edit', methods: ['GET', 'POST'])]
    public function dashboardedit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_quiz', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/quiz/modifQuiz.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'dashboard_quiz_delete', methods: ['POST'])]
    public function dashboarddelete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_quiz', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/dashboard/{id}', name: 'dashboard_quiz_show', methods: ['GET'])]
    public function dashboardshow(Quiz $quiz): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('dashboard/quiz/showQuiz.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/dashboard/quiz/{id}/stats', name: 'dashboard_quiz_stats')]
    public function dashboardstats(Quiz $quiz, EntityManagerInterface $em): Response {
        
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
            return $this->redirectToRoute('app_home');
        }

        // Convertir la Collection en tableau
        $resultats = $quiz->getResultats()->toArray();

        // Calculer le total des résultats
        $total = count($resultats);

        // Calculer la moyenne des scores
        $moyenne = $total > 0 ? array_sum(array_map(fn($r) => $r->getScore(), $resultats)) / $total : 0;

        // Trouver le meilleur score
        $meilleur = $total > 0 ? max(array_map(fn($r) => $r->getScore(), $resultats)) : 0;

        // Calculer la répartition des scores
        $repartition = array_fill(0, 10, 0); // 10 intervalles de 10%
        foreach ($resultats as $resultat) {
            $index = min((int)($resultat->getScore() / 10), 9);
            $repartition[$index]++;
        }

        return $this->render('dashboard/quiz/Quizstats.html.twig', [
            'quiz' => $quiz,
            'moyenne' => $moyenne,
            'meilleur' => $meilleur,
            'tentatives' => $total,
            'repartition' => $repartition
        ]);
    }
}
