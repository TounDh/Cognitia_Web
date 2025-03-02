<?php

namespace App\Controller;
use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use App\Form\CommentaireType;
use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;



#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        
        // Création du formulaire avec upload d'image
        $form = $this->createForm(EventType::class, $event);
                
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();
    
            if (!$dateDebut || !$dateFin) {
                $this->addFlash('error', 'Les dates de début et de fin sont obligatoires.');
                return $this->render(' dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
                
            }
    
            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
            }
    
            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('dashboard/ajoutevent.html.twig', ['form' => $form->createView()]);
            }
    
            // Gestion de l'upload d'image
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
    
            
            $entityManager->persist($commentaire);
            $entityManager->flush();
    
            
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }
    
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'commentaireForm' => $form->createView(),  
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
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté et a le rôle ROLE_PARTICIPANT
        if (!$user instanceof User || !in_array('ROLE_APPRENANT', $user->getRoles())) {
            throw new AccessDeniedException('Seuls les Apprenants peuvent s\'inscrire à cet événement.');
        }

        // Ajouter l'utilisateur comme participant
        if (!$event->getParticipants()->contains($user)) {
            $event->addParticipant($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez été inscrit à l\'événement avec succès.');
        } else {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
        }

        // Rediriger vers la page de l'événement
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    /**fonction pour l'admin */

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
        
        // Création du formulaire avec upload d'image
        $form = $this->createForm(EventType::class, $event);
                
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $titre = $event->getTitre();
            $dateDebut = $event->getDateDebut();
            $dateFin = $event->getDateFin();
    
            if (!$dateDebut || !$dateFin) {
                $this->addFlash('error', 'Les dates de début et de fin sont obligatoires.');
                return $this->render(' dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
                
            }
    
            if (!preg_match('/^[A-Z]/', $titre)) {
                $this->addFlash('error', 'Le titre doit commencer par une majuscule.');
                return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
            }
    
            if ($dateFin <= $dateDebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('dashboard/ajouteventAdmin.html.twig', ['form' => $form->createView()]);
            }
    
            // Gestion de l'upload d'image
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
