<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCatalogTest extends WebTestCase
{
    public function testCatalogLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Catalogue des livres');
    }

    public function testUserDoesNotSeeAddButton(): void
    {
        $client = static::createClient();

        $user = new User();
        $user->setEmail('user@mail.com');
        $user->setPassword('test');
        $user->setRoles(['ROLE_USER']);

        $client->loginUser($user);

        $client->request('GET', '/book');

        $this->assertSelectorNotExists('a[href="/book/new"]');
    }
}
