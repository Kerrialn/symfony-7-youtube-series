<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{


    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as $data) {
            $user = new User(email: $data['email']);
            $hashedPassword = $this->userPasswordHasher->hashPassword(user: $user,plainPassword: $data['password']);
            $user->setPassword($hashedPassword);
            $this->addReference($data['email'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }


    private function getUserData(): array
    {

        return [
            [
                'email' => 'tom@testuser.com',
                'password' => '12345678'
            ],
            [
                'email' => 'harry@testuser.com',
                'password' => '12345678'
            ],
            [
                'email' => 'james@testuser.com',
                'password' => '12345678'
            ]
        ];

    }
}
