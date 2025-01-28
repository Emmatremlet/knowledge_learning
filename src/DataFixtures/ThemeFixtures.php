<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ThemeFixtures
 *
 * Cette classe permet de charger les données de fixtures pour l'entité Theme.
 * Elle crée plusieurs thèmes en fonction des noms définis dans la constante THEMES.
 *
 * @package App\DataFixtures
 */
class ThemeFixtures extends Fixture
{
    /**
     * Liste des thèmes à créer.
     *
     * Les thèmes sont définis sous forme de chaîne de caractères représentant leurs noms.
     */
    public const THEMES = ['Musique', 'Informatique', 'Jardinage', 'Cuisine'];

    /**
     * Charge les données de fixtures pour l'entité Theme.
     *
     * Cette méthode crée des objets Theme à partir des noms définis dans la constante THEMES.
     * Chaque thème est ajouté comme référence pour permettre son association dans d'autres fixtures.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
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