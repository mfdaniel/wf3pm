<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserController|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserController|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserController[]    findAll()
 * @method UserController[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    public function usernameExist(string $username)
    {
        $queryBuilder = $this->createQueryBuilder('u');
        
        $queryBuilder->select('COUNT(u) AS count')
                ->where('u.username = :username')
                ->setParameter('username', $username);
        
        $result = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return boolval($result['count']);
    }

//    /**
//     * @return UserController[] Returns an array of UserController objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserController
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
