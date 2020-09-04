<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Event;
use App\Entity\User;
use App\Form\CreateEventFormType;
use App\Form\EventRemoveFormType;
use App\Form\EventUpdateFormType;
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event_list")
     */
    public function index(EventRepository $repository)
    {
        return $this->render('event/event_list.html.twig', [
            'event_list' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/my_event_created", name="my_event_created")
     */
    public function myEventsCreated(EventRepository $repository)
    {
        return $this->render('event/event_list.html.twig', [
            'event_list' => $repository->findBy(['author' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/my_event_participant", name="my_event_participant")
     */
    public function myEventsParticipant(EventRepository $repository)
    {
        return $this->render('event/event_list.html.twig', [
            'event_list' => $this->getUser()->getEventsParticipant(),
        ]);
    }


    /**
     * @Route("/event/create", name="event_create")
     */
    public function eventCreate(Request $request)
    {
        $form = $this->createForm(CreateEventFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer les informations saisies dans le formulaire (mais pas Author)
            $event = $form->getData();
            // Insérer l'id de l'utilisateur actuel dans l'attribut Author
            $event->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', "Votre événement a été enregistré ! ");
            return $this->redirectToRoute('my_event_created');
        }

        return $this->render('event/create_event.html.twig', [
            'eventCreateForm' => $form->createView(),
        ]);
    }

    /**
     * Page d'un événement
     * @Route ("/event/{id<\d+>}", name="event_page")
     */
    public function eventPage(Event $my_event) // 1 instance de la classe Event
    {
        return $this->render('event/event_page.html.twig', [
            'evenement' => $my_event

        ]);
    }

    /**
     * Modifier un événement
     * @Route("/event/{id<\d+>}/update", name="my_event_update")
     * @IsGranted("EVENT_CHANGE", subject="event")
     */
    public function eventUpdate(Request $request, Event $event)
    {
        $form = $this->createForm(EventUpdateFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->getData();           // getData() : mèthode qui retourne les données du formulaire

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', "Vos modifications ont été prises en compte ! ");
            return $this->redirectToRoute('event_page', ["id" => $event->getId()]);
        }

        return $this->render('event/event_update.html.twig', [
            'EventUpdateForm' => $form->createView()
        ]);
    }

    /**
     * Effacer un événement
     * @Route("/event/{id<\d+>}/remove", name="event_remove")
     * @IsGranted("EVENT_CHANGE", subject="event")
     */
    public function eventRemove(Request $request,Event $event,EntityManagerInterface $entityManager)
    {
        $removeForm = $this->createForm(EventRemoveFormType::class);
        $removeForm->handleRequest($request);

        if ($removeForm->isSubmitted() && $removeForm->isValid()) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'évenement a été supprimé');
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/event_remove.html.twig', [
            'eventRemoveForm' => $removeForm->createView(),
            'event' => $event
        ]);

    }
    /**
     * Mise à jour de la participation d'un utilisateur à un événement
     * @Route("/event/{id<\d+>}/toggle_participant", name="toggle_participant")
     */
    public function event_toggleParticipant(Event $event)
    {
        $user = $this->getUser();
        // si mon nom est dans la liste des participants, je me desinscrit
        if ($event->getParticipant()->contains($user)){
            $event->removeParticipant($user);
        } else {
            $event->addParticipant($user);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->render('event/event_page.html.twig', [
            'evenement'=> $event
        ]);
    }


}
