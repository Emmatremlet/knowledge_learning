<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Class UserRepository
 *
 * Ce repository gère les requêtes pour l'entité User.
 * Il étend la classe ServiceEntityRepository fournie par Doctrine et implémente l'interface PasswordUpgraderInterface
 * pour permettre la mise à jour des mots de passe hachés.
 *
 * @extends ServiceEntityRepository<User>
 * @package App\Repository
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     *
     * Initialise le repository avec le gestionnaire de registre pour l'entité User.
     *
     * @param ManagerRegistry $registry Le gestionnaire de registre Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Met à jour le mot de passe haché d'un utilisateur.
     *
     * Cette méthode est utilisée pour re-hacher automatiquement le mot de passe d'un utilisateur
     * lorsque nécessaire, par exemple lorsque l'algorithme de hachage évolue.
     *
     * @param PasswordAuthenticatedUserInterface $user L'utilisateur dont le mot de passe doit être mis à jour.
     * @param string $newHashedPassword Le nouveau mot de passe déjà haché.
     *
     * @throws UnsupportedUserException Si l'utilisateur donné n'est pas une instance de User.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}