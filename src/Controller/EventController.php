<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
