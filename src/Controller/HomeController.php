<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Entity\Commande;
use App\Entity\OrderBeats;
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
        if($this->getUser()){
            $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
            $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente])?$this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente]):new Commande();
        if($commande->getOrderBeats() == null){
            $orderBeats = new OrderBeats();
        }else{
            $orderBeats = $commande->getOrderBeats();
        }
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
        }else{
            $beatmakers = $userRepository->findVIP(1);
            $beats = $beatsRepository->findVIP(1);
            $instru = [];
            foreach ($beatmakers as $key => $beatmaker) {
                $instru[$beatmaker->getId()] = $beatmaker->getBeats();
        }
        return $this->render('home/index.html.twig', [
            'beatmakers' => $beatmakers,
            'beats' => $beats,
            'instru' => $instru
        ]);
        }
    }
}