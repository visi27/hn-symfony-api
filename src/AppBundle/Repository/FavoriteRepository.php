<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 11:28 AM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class FavoriteRepository extends EntityRepository
{

    public function findAllByUserQueryBuilder(User $user)
    {
        $qb = $this->createQueryBuilder('favorites');
        $qb->select('favorites.objectID');
        if ($user) {
            $qb->andWhere('favorites.user = :user')
                ->setParameter('user', $user);
        }

        return $qb;
    }
}
