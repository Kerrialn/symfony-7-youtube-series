<?php

namespace App\Factory;

use App\DataTransferObject\EventDetailsDto;
use App\DataTransferObject\EventInviationsDto;
use App\DataTransferObject\EventLocationDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;

final class EventFactory
{


    public function createFormDtos(
        User $owner,
        EventDetailsDto $eventDetailsDto,
        EventLocationDto $eventLocationDto,
        EventInviationsDto $eventInviationsDto
    ) : Event
    {

        $event = new Event(
            title: $eventDetailsDto->getTitle(),
            startAt: $eventDetailsDto->getStartAt(),
            endAt: $eventDetailsDto->getEndAt(),
            owner: $owner,
            address: $eventLocationDto->getAddress(),
            latitude: $eventLocationDto->getLongitude(),
            longitude: $eventLocationDto->getLongitude()
        );

        foreach ($eventInviationsDto->getParticipants() as $user){
            $participant = new EventParticipant(
                event: $event,
                owner: $owner,
                target: $user,
            );
            $event->addEventParticipant($participant);
        }


        return $event;

    }

}