<?php

namespace App\DataFixtures;

use App\Entity\Cursus;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LessonFixtures
 *
 * Cette classe permet de charger les données de fixtures pour l'entité Lesson.
 * Elle crée plusieurs leçons en fonction des données définies dans la constante LESSON
 * et les associe aux cursus correspondants via des références.
 *
 * @package App\DataFixtures
 */
class LessonFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Liste des leçons à créer.
     *
     * Chaque leçon contient un nom, un prix, un index correspondant à un cursus (référence),
     * une description par défaut et une URL de vidéo.
     */
    public const LESSON = [
        ['name' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'cursus' => 0],
        ['name' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'cursus' => 0],
        ['name' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'cursus' => 1],
        ['name' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'cursus' => 1],
        ['name' => 'Leçon n°1 : Les langages Html et CSS', 'price' => 32, 'cursus' => 2],
        ['name' => 'Leçon n°2 : Dynamiser votre site avec Javascript', 'price' => 32, 'cursus' => 2],
        ['name' => 'Leçon n°1 : Les outils du jardinier', 'price' => 16, 'cursus' => 3],
        ['name' => 'Leçon n°2 : Jardiner avec la lune', 'price' => 16, 'cursus' => 3],
        ['name' => 'Leçon n°1 : Les modes de cuisson', 'price' => 23, 'cursus' => 4],
        ['name' => 'Leçon n°2 : Les saveurs', 'price' => 23, 'cursus' => 4],
        ['name' => 'Leçon n°1 : Mettre en œuvre le style dans l’assiette', 'price' => 26, 'cursus' => 5],
        ['name' => 'Leçon n°2 : Harmoniser un repas à quatre plats', 'price' => 26, 'cursus' => 5],
    ];

    /**
     * Charge les données de fixtures pour l'entité Lesson.
     *
     * Cette méthode crée des objets Lesson à partir des données définies dans la constante LESSON.
     * Chaque leçon est associée à un cursus correspondant via une référence.
     * Une description par défaut et une URL de vidéo sont également ajoutées.
     * Les leçons sont ensuite persistées dans la base de données.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::LESSON as $key => $data) {
            $lesson = new Lesson();
            $lesson->setName($data['name'])
                   ->setPrice($data['price'])
                   ->setCursus($this->getReference("cursus_{$data['cursus']}", Cursus::class))
                   ->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis a.")
                   ->setVideoUrl("https://example.com/videos/lesson");

            $manager->persist($lesson);
            $this->addReference("lesson_$key", $lesson);
        }

        $manager->flush();
    }

    /**
     * Retourne les dépendances de cette fixture.
     *
     * Cette méthode permet d'indiquer que cette fixture dépend des données de la fixture CursusFixtures.
     * Cela garantit que les données des cursus seront chargées avant celles des leçons.
     *
     * @return array La liste des classes des fixtures dont dépend cette fixture.
     */
    public function getDependencies(): array
    {
        return [
            CursusFixtures::class,
        ];
    }
}