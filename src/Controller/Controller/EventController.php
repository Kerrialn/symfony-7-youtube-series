<?php

namespace App\Controller\Controller;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Form\Form\EventFormType;
use App\Form\Form\EventParticipantFormType;
use App\Repository\Event\EventParticipantRepository;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class EventController extends AbstractController
{


    public function __construct(
        private readonly EventRepository            $eventRepository,
        private readonly EventParticipantRepository $eventParticipantRepository
    )
    {
    }

    #[Route(path: '/events', name: 'create_event')]
    public function index(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $event = new Event(owner: $currentUser);
        $eventForm = $this->createForm(EventFormType::class, $event);

        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $this->eventRepository->save($event, true);
            return $this->redirectToRoute('create_event');
        }

        return $this->render('event/create.html.twig', [
            'eventForm' => $eventForm
        ]);
    }


    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventParticipant = new EventParticipant(event: $event, owner: $currentUser);
        $eventParticipantForm = $this->createForm(EventParticipantFormType::class, $eventParticipant);

        $eventParticipantForm->handleRequest($request);
        if ($eventParticipantForm->isSubmitted() && $eventParticipantForm->isValid()) {

            $this->eventParticipantRepository->save($eventParticipant, true);
            return $this->redirectToRoute('show_event', ['id' => $event->getId()]);

        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'eventParticipantForm' => $eventParticipantForm
        ]);
    }

}