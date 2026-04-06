<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 20; $i++) {
            $book = new Book();
            $pos = rand(1,6);
            $posFile = rand(1,8);
            $book->setTitle("Livre $i");
            $category = $this->getReference("category".$pos, Category::class);
            $user = $this->getReference("user".$pos, User::class);
            $book->setDescription("Description du livre $i");
            $book->setFile("Sans titre$posFile.jpeg");
            $book->setQuantite($pos);
            $book->addCategory($category);
            $book->setAuthor($user);    
            $manager->persist($book);
            $this->addReference("book".$i, $book);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class
        ];
    }
}
