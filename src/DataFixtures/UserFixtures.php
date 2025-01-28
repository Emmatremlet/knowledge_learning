<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures
 *
 * Cette classe permet de charger les données de fixtures pour l'entité User.
 * Elle crée plusieurs utilisateurs avec des rôles, des mots de passe hachés et d'autres attributs définis dans la constante USERS.
 *
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    /**
     * Instance du service de hachage des mots de passe.
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Liste des utilisateurs à créer.
     *
     * Chaque utilisateur contient un email, un mot de passe brut, un nom et des rôles.
     */
    public const USERS = [
        ['email' => 'admin@example.com', 'password' => 'password123', 'name' => 'AdminUser', 'role' => ['ROLE_ADMIN', 'ROLE_USER']],
        ['email' => 'user@example.com', 'password' => 'password1234', 'name' => 'User', 'role' => ['ROLE_USER']],
    ];

    /**
     * Constructeur de la classe.
     *
     * @param UserPasswordHasherInterface $passwordHasher Service pour hacher les mots de passe des utilisateurs.
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Charge les données de fixtures pour l'entité User.
     *
     * Cette méthode crée des utilisateurs en boucle en utilisant les données définies dans la constante USERS.
     * Chaque utilisateur a son mot de passe haché avant d'être persisté.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     */
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