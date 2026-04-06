<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function countOverlappingReservations(Book $book, \DateTimeInterface $start, \DateTimeInterface $end): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where(':book MEMBER OF r.books')
            ->andWhere('r.dateDebut <= :end')
            ->andWhere('r.dateFin >= :start')
            ->setParameter('book', $book)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function userHasPendingReservation(User $user, Book $book): bool
    {
        return (bool) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.client = :user')
            ->andWhere(':book MEMBER OF r.books')
            ->andWhere('r.status LIKE :status')
            ->setParameter('user', $user)
            ->setParameter('book', $book)
            ->setParameter('status', '%En attente%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByStatus(string $status = "En attente"): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status LIKE :status')
            ->setParameter('status', "%$status%")
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
