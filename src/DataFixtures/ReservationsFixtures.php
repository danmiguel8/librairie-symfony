<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $status = ["En attente","Rejété","En cours","Terminé"];
        for ($i=1; $i < 20; $i++) {
            $pos = rand(0,3);
            $reservation = new Reservation();
            $posUser = rand(1,6);
            $user = $this->getReference("user".$posUser, User::class);
            $boosk1 = $this->getReference("book".rand(1,19), Book::class);
            $reservation->setClient($user);
            $reservation->setDateDebut(new \DateTime());
            $reservation->setDateFin((new \DateTime())->modify('+'.rand(1,30).' days'));
            $reservation->addBook($boosk1);
            $reservation->setStatus($status[$pos]);
            $this->addReference("reservation".$i, $reservation);  

            $manager->persist($reservation);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
            BookFixtures::class
        ];
    }
}
