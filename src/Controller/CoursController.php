<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use App\Repository\ModulesRepository;
use App\Repository\DefisRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request, CoursRepository $coursRepository): Response
    {
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coursRepository->save($cour, true);
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form,
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
    public function edit(Request $request, Cours $cour, CoursRepository $coursRepository): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coursRepository->save($cour, true);
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }

    // Delete route: Delete a course
    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, CoursRepository $coursRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->get('_token'))) {
            $coursRepository->remove($cour, true);
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
