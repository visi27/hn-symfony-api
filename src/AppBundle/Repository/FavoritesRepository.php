<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 11:28 AM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FavoritesRepository extends EntityRepository
{

    public function findAllByUserQueryBuilder($userId=0)
    {
        $qb = $this->createQueryBuilder('favorites');

        if ($userId > 0) {
            $qb->andWhere('favorites.user_id = :user_id')
                ->setParameter('user_id', $userId);
        }

        return $qb;
    }
}
