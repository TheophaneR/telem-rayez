<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;


class UserRepository extends ServiceEntityRepository implements
    PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Les utilisateurs voient leur mot de passe hashés lors de leur inscription. Le meilleur algorithme de hashage est choisi par Symfony à ce moment là. Cet algorithme évolue dans le temps ce qui conduit les utilisateurs à avoir des mots de passe hashés avec différents algorithmes. On peut ainsi se retrouver avec de très anciens utilisateurs qui ont un mot de passe hashé mais avec un ancien algorithme. C'est mieux qu'ils aient tous un hash généré avec le meilleur algorithme possible. Depuis Symfony 4.4, lorsqu'un utilisateur s'authentifie avec succès, Symfony vérifie si un meilleur algorithme de hachage est disponible et hache avec ce dernier le mot de passe afin de pouvoir stocker le hachage mis à jour. Donc, même si l'utilisateur ne modifie pas son mot de passe, son hash sera quand même automatiquement mis à jour. La méthode ci-dessous sera automatiquement appelée pour enregistrer la mise à jour dans la bdd
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Les instances de "%s" ne sont pas supportées.', User::class));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
}