<?php

namespace App\Controller;

use App\Entity\Beats;
use App\Entity\Statut;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\User;
use App\Repository\BeatsRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BeatsController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/instrumental', name: 'instrumental')]
    public function index(BeatsRepository $beatsRepository, CategoryRepository $categoryRepository, Request $request, UserRepository $userRepository): Response
    {
        if ($request->query->get('search')){
            $beats = $beatsRepository->findSearch($request->query->get('search'));
        }else{
            $beats = $beatsRepository->findAll();
        };
        // $beatmakers = $userRepository->findInRoles("ROLE_BEATMAKER");
        $category = $categoryRepository->findAll();
        $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> $statut_attente,]);
        $orderBeats = $currentCommande->getOrderBeats();
        return $this->render('beats/index.html.twig', [
            'beats' => $beats,
            'panier' => $orderBeats,
            'category' => $category,
            // 'beatmakers' => $beatmakers
        ]);
    }


    #[Route('/instrumental/category/{id}', name: 'category')]
    public function category($id,CategoryRepository $categoryRepository, UserRepository $userRepository)
    {
        $category = $categoryRepository->findAll();
        $beats = $this->entityManager->getRepository(Category::class)->find($id)->getBeats();
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        // $beatmakers = $userRepository->findInRoles("ROLE_BEATMAKER");
        // foreach ($beatmakers as $beatmaker) {
        //     $beats[$beatmaker->getId()] = $beatmaker->getBeats();
        // };
        //dd($beats->toArray());
        
        return $this->render('beats/index.html.twig', [
            'beats' => $beats,
            'category' => $category,
            'panier' => $orderBeats,
            // 'beatmakers' => $beatmakers
        ]);
    }
    #[Route('/instrumental/detail/{id}', name: 'instrumental-info')]
    public function detail($id)
    {
        $beats = $this->entityManager->getRepository(Beats::class)->find($id);
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        $category = $beats->getCategory();

        return $this->render('beats/details.html.twig', [
            'beats' => $beats,
            'panier' => $orderBeats,
            'categorie' => $category,
            // 'beatmakers' => $beatmakers
        ]);
    }
}