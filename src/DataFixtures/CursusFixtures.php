<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CursusFixtures
 *
 * Cette classe permet de charger les données de fixtures pour l'entité Cursus.
 * Elle crée plusieurs cursus en fonction des données définies dans la constante CURSUS
 * et les associe aux thèmes correspondants via des références.
 *
 * @package App\DataFixtures
 */
class CursusFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Liste des cursus à créer.
     *
     * Chaque cursus contient un nom, un prix et un index correspondant à un thème (référence).
     * Les thèmes sont définis dans les fixtures ThemeFixtures.
     */
    public const CURSUS = [
        ['name' => 'Cursus d’initiation à la guitare', 'price' => 50, 'theme' => 0],
        ['name' => 'Cursus d’initiation au piano', 'price' => 50, 'theme' => 0],
        ['name' => 'Cursus d’initiation au développement web', 'price' => 60, 'theme' => 1],
        ['name' => 'Cursus d’initiation au jardinage', 'price' => 30, 'theme' => 2],
        ['name' => 'Cursus d’initiation à la cuisine', 'price' => 44, 'theme' => 3],
        ['name' => 'Cursus d’initiation à l’art du dressage culinaire', 'price' => 48, 'theme' => 3],
    ];

    /**
     * Charge les données de fixtures pour l'entité Cursus.
     *
     * Cette méthode crée des objets Cursus à partir des données définies dans la constante CURSUS.
     * Chaque cursus est associé à un thème correspondant via une référence.
     * Les cursus sont ensuite persistés dans la base de données.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::CURSUS as $key => $data) {
            $cursus = new Cursus();
            $cursus->setName($data['name'])
                   ->setPrice($data['price'])
                   ->setTheme($this->getReference("theme_{$data['theme']}", Theme::class));

            $manager->persist($cursus);

            $this->addReference("cursus_$key", $cursus);
        }

        $manager->flush();
    }

    /**
     * Retourne les dépendances de cette fixture.
     *
     * Cette méthode permet d'indiquer que cette fixture dépend des données de la fixture ThemeFixtures.
     * Cela garantit que les données des thèmes seront chargées avant celles des cursus.
     *
     * @return array La liste des classes des fixtures dont dépend cette fixture.
     */
    public function getDependencies(): array
    {
        return [
            ThemeFixtures::class,
        ];
    }
}