<?php
// src/Controller/UserProgressController.php
namespace App\Controller;

use App\Entity\UserProgress;
use App\Repository\UserProgressRepository;
use App\Repository\ModulesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProgressController extends AbstractController
{
    #[Route('/track-progress/{moduleId}', name: 'track_progress', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function trackProgress(
        int $moduleId, 
        UserRepository $userRepo, 
        ModulesRepository $moduleRepo, 
        UserProgressRepository $progressRepo, 
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser(); 
        $module = $moduleRepo->find($moduleId);

        if (!$user || !$module) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $progress = $progressRepo->findOneBy(['user' => $user, 'module' => $module]);

        if (!$progress) {
            $progress = new UserProgress();
            $progress->setUser($user);
            $progress->setModule($module);
        }

        $progress->setIsOpened(true);
        $em->persist($progress);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
