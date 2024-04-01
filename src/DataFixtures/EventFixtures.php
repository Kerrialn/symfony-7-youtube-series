<?php

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Entity\User\User;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getEventData() as $data) {
            $event = new Event(
                title: $data['title'],
                startAt: $data['startAt'],
                endAt: $data['endAt'],
                address: $data['address'],
                latitude: $data['latitude'],
                longitude: $data['longitude']
            );

            /**
             * @var User $owner
             */
            $owner = $this->getReference($data['owner']);

            $this->addReference($data['title'], $event);

            $event->setOwner($owner);
            $manager->persist($event);
        }

        $manager->flush();
    }


    private function getEventData(): array
    {
        return [
            [
                'title' => 'Henry\'s birthday party!',
                'startAt' => (new CarbonImmutable())->addWeek(),
                'endAt' => (new CarbonImmutable())->addWeek()->addHours(3),
                'address' => '42 Plainmore raod',
                'latitude' => $this->generateRandomDecimal(10, 50),
                'longitude' => $this->generateRandomDecimal(10, 50),
                'owner' => 'harry@testuser.com',
            ],
            [
                'title' => 'John\'s Leaving Party',
                'startAt' => (new CarbonImmutable())->days(2),
                'endAt' => (new CarbonImmutable())->days(2)->addHours(1),
                'address' => '41 Plainmore raod',
                'latitude' => $this->generateRandomDecimal(15, 45),
                'longitude' => $this->generateRandomDecimal(15, 45),
                'owner' => 'tom@testuser.com',
            ],
            [
                'title' => 'Tom\'s Funeral',
                'startAt' => (new CarbonImmutable())->days(1),
                'endAt' => (new CarbonImmutable())->days(1)->addHours(5),
                'address' => 'Plenath Submore 5',
                'latitude' => $this->generateRandomDecimal(15, 45),
                'longitude' => $this->generateRandomDecimal(15, 45),
                'owner' => 'tom@testuser.com',
            ]
        ];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }

    function generateRandomDecimal($min, $max, $precision = 6): int
    {
        $scale = pow(10, $precision);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }

}
