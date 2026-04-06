<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder){
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $plainPassword = 'passer123';
        for ($i = 1; $i <= 6; $i++) {
            $user = new User();
            $user->setFirstName("user".$i);
            $user->setLastName("User".$i);
            $user->setEmail("user".$i."@gmail.com");
            $encodedPassword = $this->encoder->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);
            $this->addReference("user".$i, $user);
        }

        $lubrarien = new User();
        $lubrarien->setFirstName("lubrarien");
        $lubrarien->setLastName("lubrarien");
        $lubrarien->setEmail("l@gmail.com");
        $encodedPassword = $this->encoder->hashPassword($lubrarien, $plainPassword);
        $lubrarien->setPassword($encodedPassword);
        $lubrarien->setRoles(["ROLE_LIBRARIAN"]);
        $manager->persist($lubrarien);

        $admin = new User();
        $admin->setFirstName("admin");
        $admin->setLastName("admin");
        $admin->setEmail("admin@gmail.com");
        $encodedPassword = $this->encoder->hashPassword($admin, $plainPassword);
        $admin->setPassword($encodedPassword);
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }
}
