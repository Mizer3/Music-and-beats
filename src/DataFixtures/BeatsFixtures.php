<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\Entity\Beats;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BeatsFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $category2 = $manager->getRepository(Category::class)->find(1);
        $user2 = $manager->getRepository(User::class)->find(2);
        $beats = new Beats();
        $beats->setName('Alpha chill');
        $beats->setCategory($category2);
        $beats->setUser($user2);
        $beats->setPrice(29);
        $beats->setImageName('ajax.jpg');
        $beats->setDescription('Beats de type Drake, qui passerais bien en radio.');
        $beats->setBeatName('Alpha-Chill.mp3');
        $manager->persist($beats);

        $category1 = $manager->getRepository(Category::class)->find(2);
        $user2 = $manager->getRepository(User::class)->find(2);
        $beats = new Beats();
        $beats->setName('Rain');
        $beats->setCategory($category1);
        $beats->setUser($user2);
        $beats->setPrice(29);
        $beats->setImageName('logo.png');
        $beats->setDescription('Instru type New-York des années 90, très bon rythme sur un sample de soul.');
        $beats->setBeatName('RAIN.mp3');
        $manager->persist($beats);

        $manager->flush();

    }
    public static function getGroups(): array{  
        return ['group3'];
    }
}