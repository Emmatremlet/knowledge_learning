<?php

namespace App\DataFixtures;

use App\Entity\Cursus;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LessonFixtures extends Fixture implements DependentFixtureInterface
{
        public const LESSON = [
        ['name' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'cursus' => 0, ],
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

    public function getDependencies(): array
    {
        return [
            CursusFixtures::class,
        ];
    }
}