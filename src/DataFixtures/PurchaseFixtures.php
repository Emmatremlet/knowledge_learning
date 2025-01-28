<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Purchase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PurchaseFixtures
 *
 * Cette classe permet de charger les données de fixtures pour l'entité Purchase.
 * Elle crée plusieurs achats en associant des utilisateurs à des cursus.
 *
 * @package App\DataFixtures
 */
class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Charge les données de fixtures pour l'entité Purchase.
     *
     * Cette méthode génère des achats en boucle, associant chaque utilisateur à plusieurs cursus.
     * Chaque achat comprend une date d'achat actuelle et un prix total généré aléatoirement.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
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

    /**
     * Retourne les dépendances de cette fixture.
     *
     * Cette méthode permet d'indiquer que cette fixture dépend des données des fixtures UserFixtures et CursusFixtures.
     * Cela garantit que les données des utilisateurs et des cursus seront chargées avant celles des achats.
     *
     * @return array La liste des classes des fixtures dont dépend cette fixture.
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CursusFixtures::class,
        ];
    }
}