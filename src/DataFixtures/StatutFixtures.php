<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class StatutFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $statut = new Statut();
        $statut->setName('Erreur');
        $manager->persist($statut);

        $statut = new Statut();
        $statut->setName('En attente');
        $manager->persist($statut);

        $statut = new Statut();
        $statut->setName('Validé');
        $manager->persist($statut);

        $manager->flush();
    }
    public static function getGroups(): array{  
        return ['group4'];
    }
}
