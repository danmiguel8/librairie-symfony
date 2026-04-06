<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Book;
use App\Entity\Favoris;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FavorisFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 5; $i++) {
            $fav = new Favoris();
            $pos = rand(1,6);
            $user = $this->getReference("user".$pos, User::class);
            $book = $this->getReference("book".$pos, Book::class);
            $fav->setClient($user);
            $fav->setBook($book);

            $manager->persist($fav);
            $this->addReference("fav".$i, $fav);
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
