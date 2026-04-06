<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         for ($i=1; $i < 20; $i++) {
            $category = new Category();
            $category->setTitle("Category $i");
            $category->setDescription("Description de la categorie $i");
            $manager->persist($category);
            $this->addReference("category".$i, $category);
        }

        $manager->flush();
    }
}
