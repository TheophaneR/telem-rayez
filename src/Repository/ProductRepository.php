<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder $qb queryBuilder utilisé dans les différentes méthodes
     */
    private QueryBuilder $qb;

    /**
     * @var string $alias pour l'entité manipulée
     */
    private string $alias = 'pdt';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Initialisation du QueryBuilder courant de la variable QB
     *
     * @return void
     */
    private function initializeQueryBuilder(): void
    {
        $this->qb = $this->createQueryBuilder($this->alias)->select($this->alias);
    }

    /**
     * Initialise le queryBuilder avec la fonction agrégative COUNT sur l'attribut clé primaire (il n'y a donc aucun élément NULL qui pour rappel sont ignorés par la fonction COUNT)
     *
     * @return void
     */
    private function initializeQueryBuilderWithCount(): void
    {
        $this->qb = $this
            ->createQueryBuilder($this->alias)
            ->select("COUNT($this->alias.id)");
//        s'il fallait ignorer les doublons :
//        ->select("COUNT(DISTINCT $this->alias.id)");
    }



// Méthodes retournant un QueryBuilder

    /**
     * Filtre sur une propriété avec l'opérateur LIKE à partir de la chaîne passée en argument
     *
     * @param string $propertyName
     * @param string $keyword
     * @return void
     */
    private function orPropertyLike(string $propertyName, string $keyword): void
    {
        $this->qb->orWhere("$this->alias.$propertyName LIKE :$propertyName")->setParameter($propertyName, '%' . $keyword . '%');
    }

    /**
     * Construit un QueryBuilder qui recherche tous les items contenant la chaîne passée en argument
     *
     * @param $keyword
     * @return void
     */
    private function searchQb($keyword): void
    {
        $this->orPropertyLike('description',$keyword);
        $this->orPropertyLike('name',$keyword);
    }

    /**
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword): array
    {
        $this->initializeQueryBuilder();
        $this->searchQb($keyword);

        return $this->qb->getQuery()->getResult();
    }

    /**
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function searchCount(string $keyword): int
    {
        $this->initializeQueryBuilderWithCount();
        $this->searchQb($keyword);

        return $this->qb->getQuery()->getSingleScalarResult(); // on récupère un et un seul résultat qui est un entier
    }
}