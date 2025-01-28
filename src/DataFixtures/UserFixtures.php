<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public const USERS = [
        ['email' => 'admin@example.com', 'password' => 'password123', 'name' => 'AdminUser', 'role'=>['ROLE_ADMIN, ROLE_USER']],
        ['email' => 'user@example.com', 'password' => 'password1234', 'name' => 'User', 'role'=> ['ROLE_USER']],
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $key => $userData) {
            $user = new User();
            $user->setName($userData['name'])
                 ->setEmail($userData['email'])
                 ->setRoles($userData['role'])
                 ->setPassword($this->passwordHasher->hashPassword($user, $userData['password']))
                 ->setIsVerified(true);

            $manager->persist($user);

            $this->addReference("user_$key", $user);
        }

        $manager->flush();
    }
}