<?php

namespace App\DataFixtures;

use App\DataFixtures\ReservationsFixtures;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 10; $i++) {
            $com = new Commentaire();
            $posCat = rand(1,6);
            $com->setCreated(new \DateTime());
            $com->setMessage("Message du commentaire $i");
            $com->setRaiting(rand(1,5));
            $reservation = $this->getReference("reservation".$posCat, Reservation::class);
            $com->setReservation($reservation);
            $manager->persist($com);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            ReservationsFixtures::class
        ];
    }
}
