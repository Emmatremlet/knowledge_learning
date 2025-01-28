<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public const THEMES = ['Musique', 'Informatique', 'Jardinage', 'Cuisine'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::THEMES as $key => $themeName) {
            $theme = new Theme();
            $theme->setName($themeName);

            $manager->persist($theme);

            $this->addReference("theme_$key", $theme);
        }

        $manager->flush();
    }
}