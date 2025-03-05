<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\UserProgress;
use App\Repository\CoursRepository;
use App\Repository\ModulesRepository;
use App\Repository\DefisRepository;
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
    public function new(Request $request, CoursRepository $coursRepository, EntityManagerInterface $entityManager): Response
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
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
        DefisRepository $defiRepository,
        EntityManagerInterface $entityManager // Add EntityManager
    ): Response
    {
        $cour = $coursRepository->find($id);
        
        if (!$cour) {
            throw $this->createNotFoundException('Course not found');
        }

        // Fetch modules and challenges for the course
        $modules = $cour->getModules();
        $defis = $cour->getDefis();

        // Calculate progress
        $progress = 0;
        $user = $this->getUser();

        if ($user) {
            $totalModules = $modules->count();
            $completedModules = 0;

            foreach ($modules as $module) {
                // Query UserProgress directly using EntityManager
                $userProgress = $entityManager->getRepository(UserProgress::class)->findOneBy([
                    'user' => $user,
                    'module' => $module,
                ]);

                if ($userProgress && $userProgress->isOpened()) {
                    $completedModules++;
                }
            }

            if ($totalModules > 0) {
                $progress = ($completedModules / $totalModules) * 100;
            }
        }

        return $this->render('cours/show.html.twig', [
            'cours' => $cour,
            'modules' => $modules, // Pass modules to the template
            'defis' => $defis,     // Pass defis to the template
            'progress' => $progress, // Pass progress to the template
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

    // Delete route: Delete a course
    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        // Allow both ADMIN and the course instructor to delete
        if (!$this->isGranted('ROLE_ADMIN') && 
            (!$this->isGranted('ROLE_INSTRUCTEUR') || $this->getUser() !== $cours->getInstructeur())) {
            throw new AccessDeniedException('You do not have permission to delete this course.');
        }

        if ($this->isCsrfTokenValid('delete'.$cours->getId(), $request->request->get('_token'))) {
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
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

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
}