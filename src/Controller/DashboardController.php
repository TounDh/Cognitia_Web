<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
        ]);
    }

    #[Route('/dashboard/apprenant', name: 'app_apprenant')]
    public function apprenant(): Response
    {
        return $this->render('dashboard/apprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/inst', name: 'app_inst')]
    public function inst(): Response
    {
        return $this->render('dashboard/inst.html.twig', [
        ]);
    }


    #[Route('/dashboard/cours', name: 'app_coursManage')]
    #[IsGranted('ROLE_ADMIN')]
    public function cours(CoursRepository $coursRepository): Response
    {
        $courses = $coursRepository->findAll();
        
        return $this->render('dashboard/cours.html.twig', [
            'courses' => $courses,
        ]);
    }


    #[Route('/dashboard/module', name: 'app_module')]
    public function module(): Response
    {
        return $this->render('dashboard/module.html.twig', [
        ]);
    }


    
    #[Route('/dashboard/ev', name: 'app_ev')]
    public function ev(): Response
    {
        return $this->render('dashboard/ev.html.twig', [
        ]);
    }



















    #[Route('/dashboard/commande', name: 'app_commande')]
    public function commande(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy(['archived' => false]);
        return $this->render('dashboard/commande.html.twig', [
                'commandes' => $commandes,
        ]);
    }


    #[Route('/commande/archive/{id}', name: 'commande_archive', methods: ['POST'])]
    public function archive(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setArchived(true);
        $entityManager->flush();

        $this->addFlash('success', 'La facture a été archivée avec succès.');

        return $this->redirectToRoute('app_commande');
    }

    #[Route('/dashboard/archivage', name: 'app_archive')]
    public function archivage(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy(['archived' => true]);
        return $this->render('dashboard/archive.html.twig', [
                'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/darchive/{id}', name: 'app_darchive', methods: ['POST'])]
    public function darchive(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setArchived(false);
        $entityManager->flush();

        $this->addFlash('success', 'La facture a été désarchivée avec succès.');

        return $this->redirectToRoute('app_archive');
    }

















    private $entityManager;
    private $commandeRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        CommandeRepository $commandeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->commandeRepository = $commandeRepository;
    }




    #[Route('/dashboard/risque', name: 'app_comprob')]
    public function risque(CommandeRepository $commandeRepository): Response
    {
        $commandes = $this->commandeRepository->findBy(['statut' => 'unpaid']);
        
        // Predict risk for each command
        $riskedCommandes = [];
        foreach ($commandes as $commande) {
            // Prepare input data for risk prediction
            $inputData = [
                'Historique_Retards' => $this->calculateHistoriqueRetards($commande),
                'Montant_Total' => $this->calculateMontantTotal($commande),
                'Nombre_Annulations' => $this->calculateNombreAnnulations($commande)
            ];

            // Convert input to JSON
            $inputJson = json_encode($inputData);

            // Construct the Python command
            $pythonScript = $this->getParameter('kernel.project_dir') . '/assets/models/predict_risk.py';
            
            // Create the process
            $process = new Process([
                'python',
                $pythonScript,
                $inputJson
            ]);

            try {
                // Run the process
                $process->mustRun();
            
                // Get the output and trim any whitespace
                $rawOutput = trim($process->getOutput());
                
                // Decode the JSON output
                $result = json_decode($rawOutput, true);
                
                // Debug logging
                if ($result === null) {
                    // Log JSON decoding error
                    dump('JSON Decode Error: ' . json_last_error_msg());
                    dump('Raw Output: ' . $rawOutput);
                    $commande->riskLevel = 'Unable to determine risk';
                } else {
                    // Set risk level from decoded JSON
                    $commande->riskLevel = $result['risk_level'] ?? 'Unable to determine risk';
                    $commande->riskProbabilities = $result['probabilities'] ?? [];
                }
            } catch (ProcessFailedException $exception) {
                // Log the full exception
                dump($exception->getMessage());
                
                $commande->riskLevel = 'Risk assessment failed';
                $commande->riskProbabilities = [];
            }

            $riskedCommandes[] = $commande;
        }

        // Sort commandes by risk level (optional)
        usort($riskedCommandes, function($a, $b) {
            $riskOrder = ['High Risk' => 3, 'Moderate Risk' => 2, 'Low Risk' => 1];
            return ($riskOrder[$b->riskLevel] ?? 0) - ($riskOrder[$a->riskLevel] ?? 0);
        });

        return $this->render('dashboard/comprob.html.twig', [
            'commandes' => $riskedCommandes
        ]);
    }

    #[Route('/detailed-analysis', name: 'app_risk_detailed_analysis')]
    public function detailedRiskAnalysis(): Response
    {
        // Fetch all commands for a more comprehensive analysis
        $allCommandes = $this->commandeRepository->findAll();
        
        // Aggregate risk statistics
        $riskStats = [
            'total_commands' => count($allCommandes),
            'risk_breakdown' => [
                'low_risk' => 0,
                'moderate_risk' => 0,
                'high_risk' => 0
            ],
            'total_risk_amount' => 0
        ];

        foreach ($allCommandes as $commande) {
            // Predict risk for each command
            $inputData = [
                'Historique_Retards' => $this->calculateHistoriqueRetards($commande),
                'Montant_Total' => $this->calculateMontantTotal($commande),
                'Nombre_Annulations' => $this->calculateNombreAnnulations($commande)
            ];

            $inputJson = json_encode($inputData);
            $pythonScript = $this->getParameter('kernel.project_dir') . '/assets/models/predict_risk.py';
            
            $process = new Process([
                'python',
                $pythonScript,
                $inputJson
            ]);

            try {
                $process->mustRun();
                $output = $process->getOutput();
                $result = json_decode($output, true);
                $riskLevel = $result['risk_level'] ?? 'Unable to determine risk';
            } catch (ProcessFailedException $exception) {
                $riskLevel = 'Risk assessment failed';
            }

            // Update risk statistics
            switch ($riskLevel) {
                case 'Low Risk':
                    $riskStats['risk_breakdown']['low_risk']++;
                    break;
                case 'Moderate Risk':
                    $riskStats['risk_breakdown']['moderate_risk']++;
                    break;
                case 'High Risk':
                    $riskStats['risk_breakdown']['high_risk']++;
                    $riskStats['total_risk_amount'] += $this->calculateMontantTotal($commande);
                    break;
            }
        }

        return $this->render('dashboard/commande.html.twig', [
            'risk_stats' => $riskStats
        ]);
    }

    // Helper methods for risk calculation
    private function calculateHistoriqueRetards($commande): int
{
    // Get the user - choose one of these approaches based on your entity structure
    $user = $commande->getPanier()->getUser(); // Direct user association
    

    if (!$user) {
        return 0; // Return 0 if no user is found
    }
    
    $latePayments = $this->entityManager
        ->getRepository(Commande::class)
        ->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->innerJoin('c.panier', 'p')  // Explicitly join the panier
        ->innerJoin('p.user', 'u')    // Join the user through panier
        ->where('u = :user')// Or 'c.panier.user = :user' depending on your association
        ->andWhere('c.statut = :unpaid')
        ->andWhere('c.dateAchat < :sixMonthsAgo')
        ->setParameter('user', $user)
        ->setParameter('unpaid', 'unpaid')
        ->setParameter('sixMonthsAgo', new \DateTime('-6 months'))
        ->getQuery()
        ->getSingleScalarResult();

    return (int)$latePayments;
}

    private function calculateMontantTotal($commande): float
    {
        $totalAmount = 0;
        foreach ($commande->getPanier()->getCours() as $cours) {
            $totalAmount += $cours->getPrix();
        }
        return $totalAmount;
    }

    private function calculateNombreAnnulations($commande): int
    {
        $user = $commande->getPanier()->getUser();
        
        $cancellations = $this->entityManager
            ->getRepository(\App\Entity\Commande::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->innerJoin('c.panier', 'p')  // Explicitly join the panier
            ->innerJoin('p.user', 'u')    // Join the user through panier
            ->where('u = :user')
            ->andWhere('c.statut = :cancelled')
            ->setParameter('user', $user)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$cancellations;
    }

































    #[Route('/dashboard/quiz', name: 'app_quiz')]
    public function quiz(): Response
    {
        return $this->render('dashboard/quiz.html.twig', [
        ]);
    }

    #[Route('/dashboard/contact', name: 'app_cont')]
    public function contact(): Response
    {
        return $this->render('dashboard/contact.html.twig', [
        ]);
    }
    #[Route('/dashboard/ajoutEv', name: 'app_ajoutEv')] 
    public function ajoutEv(): Response
    {
        return $this->render('dashboard/ajoutevent.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutApprenant', name: 'app_ajoutApprenant')]
    public function ajoutApprenant(): Response
    {
        return $this->render('dashboard/ajoutApprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutInstructeur', name: 'app_register_instructeur')]
    public function ajoutInstructeur(): Response
    {
        return $this->render('registration/instructeur_register.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutQuiz', name: 'app_ajoutQuiz')]
    public function ajoutQuiz(): Response
    {
        return $this->render('dashboard/ajoutQuiz.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifApprenant', name: 'app_modifApprenant')]
    public function modifApprenant(): Response
    {
        return $this->render('dashboard/modifApprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifInstructeur', name: 'app_modifInstructeur')]
    public function modifInstructeur(): Response
    {
        return $this->render('dashboard/modifInstructeur.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifQuiz', name: 'app_modifQuiz')]
    public function modifQuiz(): Response
    {
        return $this->render('dashboard/modifQuiz.html.twig', [
        ]);
    }


    
    #[Route('/dashboard', name:'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        // Seuls les utilisateurs avec le rôle ROLE_ADMIN peuvent accéder ici
        return $this->render('dashboard.html.twig');
    }

}
