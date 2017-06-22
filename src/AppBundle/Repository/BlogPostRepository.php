<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BlogPostRepository extends EntityRepository
{
    /**
     * @param string $filter
     *
     * @return QueryBuilder
     */
    public function findAllQueryBuilder($filter = '')
    {
        $qb = $this->createQueryBuilder('blog_post');

        if ($filter) {
            $qb->andWhere('blog_post.title LIKE :filter OR blog_post.content LIKE :filter')
                ->setParameter('filter', '%'.$filter.'%');
        }

        return $qb;
    }

    public function findAllPublishedOrderedByPublishedDate()
    {
        return $this->createQueryBuilder('blog_post')
            ->andWhere('blog_post.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->orderBy('blog_post.publishedAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findAllByCategoryQueryBuilder(Category $category)
    {
        $qb = $this->createQueryBuilder('blog_post');

        if ($category) {
            $qb->andWhere('blog_post.category = :category')
                ->setParameter('category', $category);
        }

        return $qb;
    }
}
