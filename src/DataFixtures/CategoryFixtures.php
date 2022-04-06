<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Boom Bap');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Pop Rap');
        $manager->persist($category);

        $manager->flush();
    }
    public static function getGroups(): array{  
        return ['group2'];
    }
}
