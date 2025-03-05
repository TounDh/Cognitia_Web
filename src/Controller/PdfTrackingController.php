<?php
namespace App\Controller;

use App\Entity\UserProgress;
use App\Entity\Modules;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/track-pdf-open')]
class PdfTrackingController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/{id}', name: 'track_pdf_open', methods: ['POST'])]
    public function trackPdfOpen(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not logged in'], 401);
        }

        $module = $this->entityManager->getRepository(Modules::class)->find($id);
        if (!$module) {
            return new JsonResponse(['status' => 'error', 'message' => 'Module not found'], 404);
        }

        // Check if progress already exists
        $progress = $this->entityManager->getRepository(UserProgress::class)->findOneBy([
            'user' => $user,
            'module' => $module
        ]);

        if (!$progress) {
            $progress = new UserProgress();
            $progress->setUser($user);
            $progress->setModule($module);
        }

        $progress->setIsOpened(true);
        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}