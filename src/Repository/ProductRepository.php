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

// Initialisation du QueryBuilder courant de la variable QB
    private function initializeQueryBuilder(): void
    {
        $this->qb = $this->createQueryBuilder($this->alias)->select($this->alias);
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
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword): array
    {
        $this->initializeQueryBuilder();
        $this->orPropertyLike('name', $keyword);
        $this->orPropertyLike('description', $keyword);

        return $this->qb->getQuery()->getResult();
    }
}