<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('category')
            ->orderBy('category.name', 'ASC');
    }

    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('category');

        if ($filter) {
            $qb->andWhere('category.name LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        return $qb;
    }
}
