<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\User;
use App\Model\ListOfUsersFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
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

    public function findLeastBusyModerator(News $news): ?User
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->leftJoin('u.categoriesToModerate', 'c')
            ->leftJoin('u.newsToModerate', 'n')
            ->where('u.roles LIKE :moderatorRole')
            ->andWhere('c.id IN (:categories)')
            ->andWhere('n.status = :toModerateStatus')
            ->setParameter('categories', $news->getCategories())
            ->setParameter('moderatorRole', '%' . User::ROLE_MODERATOR . '%')
            ->setParameter('toModerateStatus', News::STATUS_TO_MODERATE)
            ->orderBy('COUNT(n.id)', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByFilter(?ListOfUsersFilter $filter, int $offset, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('u');

        if (!is_null($filter)) {
            if (!is_null($id = $filter->id)) {
                $query
                    ->andWhere('u.id = :id')
                    ->setParameter('id', $id);
            }
            if (!is_null($email = $filter->email)) {
                $query
                    ->andWhere('u.email = :email')
                    ->setParameter('email', $email);
            }
        }

        $query
            ->orderBy('u.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function countByFilter(?ListOfUsersFilter $filter): int
    {
        $condition = [];

        if (!is_null($filter)) {
            if (!is_null($filter->id)) {
                $condition['id'] = $filter->id;
            }

            if (!is_null($filter->email)) {
                $condition['email'] = $filter->email;
            }
        }

        return $this->count($condition);
    }

    public function existsByEmail(string $email): bool
    {
        return (bool) $this->findOneBy(['email' => $email]);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
