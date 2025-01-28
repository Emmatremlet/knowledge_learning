<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CursusFixtures extends Fixture implements DependentFixtureInterface
{
    public const CURSUS = [
        ['name' => 'Cursus d’initiation à la guitare', 'price' => 50, 'theme' => 0],
        ['name' => 'Cursus d’initiation au piano', 'price' => 50, 'theme' => 0],
        ['name' => 'Cursus d’initiation au développement web', 'price' => 60, 'theme' => 1],
        ['name' => 'Cursus d’initiation au jardinage', 'price' => 30, 'theme' => 2],
        ['name' => 'Cursus d’initiation à la cuisine', 'price' => 44, 'theme' => 3],
        ['name' => 'Cursus d’initiation à l’art du dressage culinaire', 'price' => 48, 'theme' => 3],

    ];

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

    public function getDependencies(): array
    {
        return [
            ThemeFixtures::class,
        ];
    }
}