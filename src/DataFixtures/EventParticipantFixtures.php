<?php

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Enum\EventParticipantTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventParticipantFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager): void
    {
        foreach ($this->getEventParcitipantData() as $data) {

            /**
             * @var Event $event
             */
            $event = $this->getReference($data['title']);

            /**
             * @var User $owner
             */
            $owner = $this->getReference($data['owner']);

            /**
             * @var User $owner
             */
            $target = $this->getReference($data['target']);

            $eventParticipant = new EventParticipant(
                event: $event,
                owner: $owner,
                target: $target,
                type: EventParticipantTypeEnum::EVENT_PARTICIPANT
            );

            $event->addEventParticipant($eventParticipant);
            $manager->persist($event);
        }

        $manager->flush();
    }


    private function getEventParcitipantData(): array
    {

        return [
            [
                'title' => 'John\'s Leaving Party',
                'owner' => 'tom@testuser.com',
                'target' => 'james@testuser.com'
            ],
            [
                'title' => 'John\'s Leaving Party',
                'owner' => 'tom@testuser.com',
                'target' => 'james@testuser.com'
            ],
        ];

    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            EventFixtures::class
        ];
    }
}
