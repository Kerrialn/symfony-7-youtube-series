<?php

namespace App\Controller\Controller;

use App\DataTransferObject\EventDetailsDto;
use App\DataTransferObject\EventInviationsDto;
use App\DataTransferObject\EventLocationDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Factory\EventFactory;
use App\Form\Form\Event\EventDetailsFormType;
use App\Form\Form\Event\EventInviationsFormType;
use App\Form\Form\Event\EventLocationFormType;
use App\Form\Form\EventParticipantFormType;
use App\Repository\Event\EventParticipantRepository;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class EventController extends AbstractController
{
    private const EVENT_CREATE_STEP_ONE = 'details';
    private const EVENT_CREATE_STEP_TWO = 'location';
    private const EVENT_CREATE_STEP_THREE = 'invitations';


    public function __construct(
        private readonly EventRepository            $eventRepository,
        private readonly EventFactory               $eventFactory,
        private readonly EventParticipantRepository $eventParticipantRepository,
        private readonly RequestStack               $requestStack
    )
    {
    }

    #[Route(path: '/events', name: 'events')]
    public function index(Request $request, #[CurrentUser] User $currentUser): Response
    {
        return $this->render('event/index.html.twig');
    }

    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $eventParticipant = new EventParticipant(event: $event, owner: $currentUser);
        $eventParticipantForm = $this->createForm(EventParticipantFormType::class, $eventParticipant);

        $eventParticipantForm->handleRequest($request);
        if ($eventParticipantForm->isSubmitted() && $eventParticipantForm->isValid()) {
            $this->eventParticipantRepository->save($eventParticipant, true);
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'eventParticipantForm' => $eventParticipantForm,
        ]);
    }


    #[Route(path: '/events/create/{step}', name: 'create_event')]
    public function create(string $step, Request $request, #[CurrentUser] User $currentUser): Response
    {

        $form = match ($step) {
            self::EVENT_CREATE_STEP_ONE => $this->renderEventCreateFromStepOne(),
            self::EVENT_CREATE_STEP_TWO => $this->renderEventCreateFromStepTwo(),
            self::EVENT_CREATE_STEP_THREE => $this->renderEventCreateFromStepThree(),
            default => $this->redirectToRoute('create_event', ['step' => self::EVENT_CREATE_STEP_ONE])
        };

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return match (true) {
                $step === self::EVENT_CREATE_STEP_ONE => $this->handleEventFormStepOne($form),
                $step === self::EVENT_CREATE_STEP_TWO => $this->handleEventFormStepTwo($form),
                $step === self::EVENT_CREATE_STEP_THREE => $this->handleEventFormStepThree($form, $currentUser),
                default => $this->redirectToRoute('create_event', ['step' => self::EVENT_CREATE_STEP_ONE])
            };
        }

        return $this->render(sprintf('event/create/step-%s.html.twig', $step), [
            'form' => $form,
            'data' => $form->getData()
        ]);

    }

    private function renderEventCreateFromStepOne(): FormInterface
    {
        $eventDetailsDto = $this->requestStack->getSession()->get('event-form-step-one');

        if (!$eventDetailsDto instanceof EventDetailsDto) {
            $eventDetailsDto = new EventDetailsDto();
        }

        return $this->createForm(EventDetailsFormType::class, $eventDetailsDto);
    }


    private function renderEventCreateFromStepTwo(): FormInterface
    {
        $eventLocationDto = $this->requestStack->getSession()->get('event-form-step-two');

        if (!$eventLocationDto instanceof EventLocationDto) {
            $eventLocationDto = new EventLocationDto();
        }

        return $this->createForm(EventLocationFormType::class, $eventLocationDto);
    }


    private function renderEventCreateFromStepThree(): FormInterface
    {
        $eventInviationsDto = $this->requestStack->getSession()->get('event-form-step-three');

        if (!$eventInviationsDto instanceof EventInviationsDto) {
            $eventInviationsDto = new EventInviationsDto();
        }

        return $this->createForm(EventInviationsFormType::class, $eventInviationsDto);
    }

    private function handleEventFormStepOne(FormInterface $form): Response
    {
        $this->requestStack->getSession()->set('event-form-step-one', $form->getData());
        return $this->redirectToRoute('create_event', ['step' => self::EVENT_CREATE_STEP_TWO]);
    }


    private function handleEventFormStepTwo(FormInterface $form): Response
    {
        $this->requestStack->getSession()->set('event-form-step-two', $form->getData());
        return $this->redirectToRoute('create_event', ['step' => self::EVENT_CREATE_STEP_THREE]);
    }

    private function handleEventFormStepThree(FormInterface $form, User $user): Response
    {
        /**
         * @var EventDetailsDto $eventDetailsDto
         */
        $eventDetailsDto = $this->requestStack->getSession()->get('event-form-step-one');

        /**
         * @var EventLocationDto $eventLocationDto
         */
        $eventLocationDto = $this->requestStack->getSession()->get('event-form-step-two');

        $event = $this->eventFactory->createFormDtos(
            owner: $user,
            eventDetailsDto: $eventDetailsDto,
            eventLocationDto: $eventLocationDto,
            eventInviationsDto: $form->getData()
        );

        $this->eventRepository->save($event, true);

        $this->requestStack->getSession()->set('event-form-step-one', null);
        $this->requestStack->getSession()->set('event-form-step-two', null);

        return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
    }

}