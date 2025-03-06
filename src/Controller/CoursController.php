<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\User;
use App\Entity\UserProgress;
use App\Repository\CoursRepository;
use App\Repository\ModulesRepository;
use App\Repository\DefisRepository;
use App\Service\CertificateGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CoursType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/cours')]
final class CoursController extends AbstractController
{
    private $entityManager;
    private $certificateGenerator;

    public function __construct(EntityManagerInterface $entityManager, CertificateGenerator $certificateGenerator)
    {
        $this->entityManager = $entityManager;
        $this->certificateGenerator = $certificateGenerator;
    }

    // Index route: Display all courses
    #[Route(name: 'app_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    // New course route: Add a new course
    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function new(Request $request, CoursRepository $coursRepository): Response
    {
        $cours = new Cours();
        $cours->setInstructeur($this->getUser());
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('cours_images_directory'),
                        $newFilename
                    );

                    $cours->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $coursRepository->save($cours, true);
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/new.html.twig', [
            'cours' => $cours,
            'form' => $form->createView(),
        ]);
    }

    // Show route: Display details for a specific course with modules and challenges
    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(
        int $id,
        CoursRepository $coursRepository,
        ModulesRepository $moduleRepository,
        DefisRepository $defiRepository
    ): Response {
        $cour = $coursRepository->find($id);

        if (!$cour) {
            throw $this->createNotFoundException('Course not found');
        }

        // Fetch modules and challenges for the course
        $modules = $cour->getModules();
        $defis = $cour->getDefis();

        // Calculate progress
        $progress = $this->calculateProgress($cour);

        return $this->render('cours/show.html.twig', [
            'cours' => $cour,
            'modules' => $modules,
            'defis' => $defis,
            'progress' => $progress,
        ]);
    }

    // Edit route: Edit an existing course
    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        // Allow both ADMIN and the course instructor to edit
        if (!$this->isGranted('ROLE_ADMIN') &&
            (!$this->isGranted('ROLE_INSTRUCTEUR') || $this->getUser() !== $cours->getInstructeur())) {
            throw new AccessDeniedException('You do not have permission to edit this course.');
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cours,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        // Allow both ADMIN and the course instructor to delete
        if (!$this->isGranted('ROLE_ADMIN') &&
            (!$this->isGranted('ROLE_INSTRUCTEUR') || $this->getUser() !== $cours->getInstructeur())) {
            throw new AccessDeniedException('You do not have permission to delete this course.');
        }
    
        if ($this->isCsrfTokenValid('delete' . $cours->getId(), $request->request->get('_token'))) {
            // Get the repository for user_progress (assuming it's an entity)
            $userProgressRepo = $entityManager->getRepository(UserProgress::class);
    
            // Delete user_progress entries related to this course's modules
            foreach ($cours->getModules() as $module) {
                $userProgressEntries = $userProgressRepo->findBy(['module' => $module]);
                foreach ($userProgressEntries as $entry) {
                    $entityManager->remove($entry);
                }
            }
    
            // Flush to remove user progress before deleting modules
            $entityManager->flush();
    
            // Now remove related Modules
            foreach ($cours->getModules() as $module) {
                $entityManager->remove($module);
            }
    
            // Remove related Defis (Challenges)
            foreach ($cours->getDefis() as $defi) {
                $entityManager->remove($defi);
            }
    
            // Ensure all deletions are flushed before deleting the course
            $entityManager->flush();
    
            // Now remove the course itself
            $entityManager->remove($cours);
            $entityManager->flush();
        }
    
        // Redirect back to dashboard if coming from there
        $referer = $request->headers->get('referer');
        if (strpos($referer, 'dashboard/cours') !== false) {
            return $this->redirectToRoute('app_coursManage');
        }
    
        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
    
    
    

    // Add this new route for dashboard course creation
    #[Route('/dashboard/cours/new', name: 'app_cours_dashboard_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newInDashboard(Request $request, CoursRepository $coursRepository): Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('cours_images_directory'),
                        $newFilename
                    );

                    $cours->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                }
            }

            $coursRepository->save($cours, true);
            $this->addFlash('success', 'Course created successfully!');
            return $this->redirectToRoute('app_coursManage');
        }

        return $this->render('dashboard/cours_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/cours/{id}/edit', name: 'app_cours_dashboard_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editInDashboard(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('cours_images_directory'),
                        $newFilename
                    );
                    $cours->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Course updated successfully!');
            return $this->redirectToRoute('app_coursManage');
        }

        return $this->render('dashboard/cours_edit.html.twig', [
            'cour' => $cours,
            'form' => $form->createView(),
        ]);
    }

    // Certificate download route
    #[Route('/{id}/certificate', name: 'app_cours_certificate', methods: ['GET'])]
    public function downloadCertificate(
        int $id,
        CoursRepository $coursRepository
    ): Response {
        $cour = $coursRepository->find($id);
        if (!$cour) {
            throw $this->createNotFoundException('Course not found');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to download the certificate.');
        }

        // Ensure the user has completed the course (progress = 100%)
        $progress = $this->calculateProgress($cour);
        if ($progress < 100) {
            throw $this->createAccessDeniedException('You must complete the course to download the certificate.');
        }

        // Generate and return the certificate
        return $this->certificateGenerator->generateCertificate(
            $user->getFirstName(), // Replace with the user's name
            $cour->getTitre()
        );
      
    }
    

    /**
     * Calculate the progress of the current user in a course.
     */
    private function calculateProgress(Cours $cour): float
    {
        $user = $this->getUser();
        if (!$user) {
            return 0; // No user, no progress
        }

        $totalModules = $cour->getModules()->count();
        $completedModules = 0;

        foreach ($cour->getModules() as $module) {
            // Query UserProgress directly using EntityManager
            $userProgress = $this->entityManager->getRepository(UserProgress::class)->findOneBy([
                'user' => $user,
                'module' => $module,
            ]);

            if ($userProgress && $userProgress->isOpened()) {
                $completedModules++;
            }
        }

        return $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;
    }
}