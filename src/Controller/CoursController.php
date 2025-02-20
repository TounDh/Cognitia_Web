<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use App\Repository\ModulesRepository;
use App\Repository\DefisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CoursType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

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
        DefisRepository $defiRepository
    ): Response
    {
        $cour = $coursRepository->find($id);
        
        if (!$cour) {
            throw $this->createNotFoundException('Course not found');
        }

        // Fetch modules and challenges for the course
        $modules = $cour->getModules();
        $defis = $cour->getDefis(); 

        return $this->render('cours/show.html.twig', [
            'cours' => $cour,
            'modules' => $modules, // Pass modules to the template
            'defis' => $defis,     // Pass defis to the template
        ]);
    }

    // Edit route: Edit an existing course
    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_INSTRUCTEUR');
        
        if ($this->getUser() !== $cours->getInstructeur()) {
            throw new AccessDeniedException('You can only edit your own courses.');
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
        $this->denyAccessUnlessGranted('ROLE_INSTRUCTEUR');
        
        if ($this->getUser() !== $cours->getInstructeur()) {
            throw new AccessDeniedException('You can only delete your own courses.');
        }

        if ($this->isCsrfTokenValid('delete'.$cours->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cours);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
