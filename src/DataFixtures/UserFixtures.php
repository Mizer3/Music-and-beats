<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->encoder = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('Arnaud.derain@gmail.com');
        $password = $this->encoder->hashPassword($user, "root");
        $user->setPassword($password);
        $user->setNom('Derain');
        $user->setPrenom('Arnaud');
        $user->setPseudo('Mizerito75');
        $user->setAdresse('8 avenue des tilleuls');
        $user->setCodePostal('95350');
        $user->setImageName('ajax.jpg');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('arnaud3_8@hotmail.com');
        $password = $this->encoder->hashPassword($user, "root");
        $user->setPassword($password);
        $user->setNom('Derain');
        $user->setPrenom('Arnaud');
        $user->setPseudo('Mizer3');
        $user->setAdresse('8 avenue des tilleuls');
        $user->setCodePostal('95350');
        $user->setImageName('ajax.jpg');
        $user->setRoles(['ROLE_USER', 'ROLE_BEATMAKER']);
        $user->setIsVerified(true);
        $manager->persist($user);

        $manager->flush();
    }
    public static function getGroups(): array{  
        return ['group1'];
    }
}
