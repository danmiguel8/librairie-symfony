<?php

namespace App\Tests;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Reservation;
use App\Entity\Favoris;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testCreateBook(): void
    {
        $book = new Book();

        $author = new User();
        $author->setEmail('author@mail.com');
        $author->setFirstName('John');
        $author->setLastName('Doe');

        $category1 = new Category();
        $category1->setTitle('Roman');

        $category2 = new Category();
        $category2->setTitle('Aventure');

        $reservation = new Reservation();

        $favoris = new Favoris();

        $book->setTitle('Le Seigneur des Anneaux');
        $book->setAuthor($author);
        $book->addCategory($category1);
        $book->addCategory($category2);
        $book->setDescription('Un roman épique de fantasy.');
        $book->setFile('cover.jpg');
        $book->setQuantite(5);
        $book->addReservation($reservation);
        $book->addFavori($favoris);

        $this->assertEquals('Le Seigneur des Anneaux', $book->getTitle());
        $this->assertEquals($author, $book->getAuthor());
        $this->assertCount(2, $book->getCategory());
        $this->assertEquals('Un roman épique de fantasy.', $book->getDescription());
        $this->assertEquals('cover.jpg', $book->getFile());
        $this->assertEquals(5, $book->getQuantite());
        $this->assertCount(1, $book->getReservations());
        $this->assertCount(1, $book->getFavoris());
    }
}
