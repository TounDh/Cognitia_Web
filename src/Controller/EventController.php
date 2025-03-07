<?php
namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Event;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\ProfanityFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Service\DiscordService;

#[Route('/event')]
final class EventController extends AbstractController

{
    private ProfanityFilterService $profanityFilterService;
    private DiscordService $discordService;

    public function __construct(ProfanityFilterService $profanityFilterService,DiscordService $discordService)
    {
        $this->profanityFilterService = $profanityFilterService;
        $this->discordService = $discordService;

    }

    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_event_search', methods: ['GET'])]
    public function search(Request $request, EventRepository $eventRepository): Response
    {
        $search = $request->query->get('q', '');
        $sort = $request->query->get('sort', 'titre');
        $direction = $request->query->get('direction', 'asc');

        $events = $eventRepository->findByFiltersAndSort($search, null, null, $sort, $direction);

        return $this->render('event/_event_list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();

            if (!$dateDebut || !$dateFin) {
                $this->addFlash('error', 'Les dates de début et de fin sont obligatoires.');
                return $this->render('dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
            }

            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
            }

            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
            }

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $event->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();

            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('event/edit.html.twig', [
                    'form' => $form->createView(),
                    'event' => $event,
                ]);
            }

            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('event/edit.html.twig', [
                    'form' => $form->createView(),
                    'event' => $event,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setEvenement($event);

            // Apply profanity filter
            $censoredTitle = $this->profanityFilterService->censorText($commentaire->getTitre());
            $censoredContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setTitre($censoredTitle);
            $commentaire->setContenu($censoredContent);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }
        $inviteLink = $this->discordService->createEventChannel($event->getTitre(), $event->getId());


        return $this->render('event/show.html.twig', [
            'event' => $event,
            'commentaireForm' => $form->createView(),
            'inviteLink' => $inviteLink,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_event_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/event/{id}/participate', name: 'event_participate', methods: ['POST'])]
    public function participate(Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !in_array('ROLE_APPRENANT', $user->getRoles())) {
            throw new AccessDeniedException('Seuls les Apprenants peuvent s\'inscrire à cet événement.');
        }

        if (!$event->getParticipants()->contains($user)) {
            $event->addParticipant($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez été inscrit à l\'événement avec succès.');
        } else {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/dashboard/event', name: 'dashboard_event_index', methods: ['GET'])]
    public function dashboardindex(EventRepository $eventRepository): Response
    {
        return $this->render('dashboard/eventDashboard.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/new', name: 'dashboard_event_new', methods: ['GET', 'POST'])]
    public function dashboardnew(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();

            if (!$dateDebut || !$dateFin) {
                $this->addFlash('error', 'Les dates de début et de fin sont obligatoires.');
                return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
            }

            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
            }

            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
            }

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $event->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_event_index');
        }

        return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/dashboard/{id}/edit', name: 'dashboard_event_edit', methods: ['GET', 'POST'])]
    public function dashboardedit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();

            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('dashboard/editEvent.html.twig', [
                    'form' => $form->createView(),
                    'event' => $event,
                ]);
            }

            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('dashboard/editEvent.html.twig', [
                    'form' => $form->createView(),
                    'event' => $event,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('dashboard_event_index');
        }

        return $this->render('dashboard/editEvent.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'dashboard_event_show', methods: ['GET', 'POST'])]
    public function dashboardshow(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setEvenement($event);

            // Apply profanity filter
            $censoredTitle = $this->profanityFilterService->censorText($commentaire->getTitre());
            $censoredContent = $this->profanityFilterService->censorText($commentaire->getContenu());
            $commentaire->setTitre($censoredTitle);
            $commentaire->setContenu($censoredContent);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_event_show', ['id' => $event->getId()]);
        }

        return $this->render('dashboard/showEvent.html.twig', [
            'event' => $event,
            'commentaireForm' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/{id}/delete', name: 'dashboard_event_delete', methods: ['POST', 'DELETE'])]
    public function dashboarddelete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_event_index');
    }
    
}