<?php

namespace App\Controller;

use App\Entity\Modules;
use App\Form\ModulesType;
use App\Repository\ModulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/modules')]
final class ModulesController extends AbstractController
{
    #[Route(name: 'app_modules_index', methods: ['GET'])]
    public function index(ModulesRepository $modulesRepository): Response
    {
        return $this->render('modules/index.html.twig', [
            'modules' => $modulesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_modules_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $module = new Modules();
        $form = $this->createForm(ModulesType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($module);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modules/new.html.twig', [
            'module' => $module,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modules_show', methods: ['GET'])]
    public function show(Modules $module): Response
    {
        return $this->render('modules/show.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_modules_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Modules $module, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModulesType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modules/edit.html.twig', [
            'module' => $module,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modules_delete', methods: ['POST'])]
    public function delete(Request $request, Modules $module, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$module->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($module);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
