<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public function testLogin(): void
    {
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setPassword('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        
        $this->assertEquals('admin@gmail.com', $user->getEmail());
        $this->assertEquals('admin', $user->getPassword());
        $this->assertTrue(in_array('ROLE_ADMIN', $user->getRoles()));
        $this->assertEquals('Admin', $user->getFirstName());
        $this->assertEquals('Admin', $user->getLastName());
    }
}
