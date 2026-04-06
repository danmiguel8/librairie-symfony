<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function searchPaginated(string $value, int $page = 1, int $limit = 8): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->leftJoin('b.category', 'c')
            ->addSelect('a', 'c')
            ->where('b.title LIKE :val')
            ->orWhere('a.firstName LIKE :val')
            ->orWhere('a.lastName LIKE :val')
            ->orWhere('c.title LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('b.id', 'DESC');

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function findAllPaginate(int $page = 1, int $limit = 8): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC');

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function findAvailableBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.quantite >= 1')
            ->getQuery()
            ->getResult();
    }




//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
