<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Purchase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(0, 1) as $userIndex) {
            foreach (range(0, 2) as $cursusIndex) {
                $purchase = new Purchase();
                $purchase->setUser($this->getReference("user_$userIndex", User::class))
                         ->setCursus($this->getReference("cursus_$cursusIndex", Cursus::class))
                         ->setPurchaseDate(new \DateTimeImmutable())
                         ->setTotalPrice(mt_rand(50, 150));

                $manager->persist($purchase);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CursusFixtures::class,
        ];
    }
}