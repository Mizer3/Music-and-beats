<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Statut;
use App\Entity\Commande;
use App\Repository\UserRepository;
use App\Repository\BeatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/home', name: 'home')]
    public function index(UserRepository $userRepository, BeatsRepository $beatsRepository): Response
    {
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        $beatmakers = $userRepository->findVIP(1);
        $beats = $beatsRepository->findVIP(1);
        $instru = [];
        foreach ($beatmakers as $key => $beatmaker) {
            $instru[$beatmaker->getId()] = $beatmaker->getBeats();
        }
        return $this->render('home/index.html.twig', [
            'panier' => $orderBeats,
            'beatmakers' => $beatmakers,
            'beats' => $beats,
            'instru' => $instru
        ]);
    }
}