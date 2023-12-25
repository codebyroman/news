<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\User;
use App\Model\ListOfNewsFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findByFilter(?ListOfNewsFilter $filter, int $offset, int $limit): Paginator
    {
        // TODO improve filters
        $query = $this->createQueryBuilder('n')
                    ->leftJoin('n.categories', 'c');

        if (!is_null($filter)) {
            if (!is_null($id = $filter->id)) {
                $query
                    ->andWhere('n.id = :id')
                    ->setParameter('id', $id);
            }
            if (!is_null($title = $filter->title)) {
                $query
                    ->andWhere('n.title = :title')
                    ->setParameter('title', $title);
            }
        }

        $query
            ->orderBy('u.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function countByFilter(?ListOfNewsFilter $filter)
    {
        $condition = [];

        if (!is_null($filter)) {
            if (!is_null($filter->id)) {
                $condition['id'] = $filter->id;
            }

            if (!is_null($filter->title)) {
                $condition['title'] = $filter->title;
            }
        }

        return $this->count($condition);
    }

//    /**
//     * @return News[] Returns an array of News objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?News
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
