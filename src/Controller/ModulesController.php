<?php
namespace App\Controller;

use App\Entity\Modules;
use App\Entity\UserProgress;
use App\Repository\UserProgressRepository;
use App\Form\ModulesType;
use App\Repository\ModulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/modules')]
class ModulesController extends AbstractController
{
    #[Route('/', name: 'app_modules_index', methods: ['GET'])]
    public function index(ModulesRepository $modulesRepository): Response
    {
        return $this->render('modules/index.html.twig', [
            'modules' => $modulesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_modules_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $module = new Modules();
        $form = $this->createForm(ModulesType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('modules_pdfs_directory'),
                        $newFilename
                    );

                    $module->setPdfPath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the PDF file.');
                }
            }

            $entityManager->persist($module);
            $entityManager->flush();

            $this->addFlash('success', 'Module created successfully.');
            return $this->redirectToRoute('app_modules_index');
        }

        return $this->render('modules/new.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_modules_show', methods: ['GET'])]
    public function show(Modules $module, EntityManagerInterface $entityManager, UserProgressRepository $userProgressRepository): Response
    {
        $user = $this->getUser();

        if ($user) {
            $progress = $userProgressRepository->findOneBy([
                'user' => $user,
                'module' => $module
            ]);

            if (!$progress) {
                $progress = new UserProgress();
                $progress->setUser($user);
                $progress->setModule($module);
                $progress->setIsOpened(true);
                $entityManager->persist($progress);
            } elseif (!$progress->isOpened()) {
                $progress->setIsOpened(true);
            }

            $entityManager->flush();
        }

        return $this->render('modules/show.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_modules_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function edit(Request $request, Modules $module, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModulesType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('modules_pdfs_directory'),
                        $newFilename
                    );

                    $module->setPdfPath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the PDF file.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Module updated successfully.');
            return $this->redirectToRoute('app_modules_index');
        }

        return $this->render('modules/edit.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_modules_delete', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTEUR')]
    public function delete(Request $request, Modules $module, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $module->getId(), $request->request->get('_token'))) {
            if ($module->getPdfPath()) {
                $filePath = $this->getParameter('modules_pdfs_directory') . '/' . $module->getPdfPath();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $entityManager->remove($module);
            $entityManager->flush();

            $this->addFlash('success', 'Module deleted successfully.');
        }

        return $this->redirectToRoute('app_modules_index');
    }
}
