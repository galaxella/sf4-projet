<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CreateEventFormType;
use App\Form\ProfilUpdateFormType;
use App\Repository\EventRepository;
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
     * @Route ("/event/{id}", name="event_page")
     */
    public function eventPage(Event $my_event) // 1 instance de la classe Event
    {
        return $this->render('event/event_page.html.twig', [
            'evenement' => $my_event

        ]);
    }


}
