<?php

namespace App\Controller;

use App\Entity\Beats;
use App\Entity\Statut;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\OrderBeats;
use App\Repository\UserRepository;
use App\Repository\BeatsRepository;
use App\Repository\CategoryRepository;
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
    public function index(BeatsRepository $beatsRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        if($this->getUser()){
            if ($request->query->get('search')){
                $beats = $beatsRepository->findSearch($request->query->get('search'));
            }else{
                $beats = $beatsRepository->findAll();
            };
            $category = $categoryRepository->findAll();
            $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
            $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente])?$this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente]):new Commande();
            if($commande->getOrderBeats() == null){
                $orderBeats = new OrderBeats();
            }else{  
                $orderBeats = $commande->getOrderBeats();
            }
            return $this->render('beats/index.html.twig', [
                'beats' => $beats,
                'panier' => $orderBeats,
                'category' => $category,
            ]);
        }else{
            if ($request->query->get('search')){
                $beats = $beatsRepository->findSearch($request->query->get('search'));
            }else{
                $beats = $beatsRepository->findAll();
            };
            $category = $categoryRepository->findAll();
            return $this->render('beats/index.html.twig', [
                'beats' => $beats,
                'category' => $category,
            ]);
        }
    }


    #[Route('/instrumental/category/{id}', name: 'category')]
    public function category($id,CategoryRepository $categoryRepository)
    {
        if($this->getUser()){
            $category = $categoryRepository->findAll();
            $beats = $this->entityManager->getRepository(Category::class)->find($id)->getBeats();
            $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
            $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente])?$this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente]):new Commande();
            if($commande->getOrderBeats() == null){
                $orderBeats = new OrderBeats();
            }else{
                $orderBeats = $commande->getOrderBeats();
            }

            return $this->render('beats/index.html.twig', [
                'beats' => $beats,
                'category' => $category,
                'panier' => $orderBeats,
            ]);
        }else{
            $category = $categoryRepository->findAll();
            $beats = $this->entityManager->getRepository(Category::class)->find($id)->getBeats();

            return $this->render('beats/index.html.twig', [
                'beats' => $beats,
                'category' => $category,
            ]);
        }
    }
    #[Route('/instrumental/detail/{id}', name: 'instrumental-info')]
    public function detail($id)
    {
        if($this->getUser()){
            $beats = $this->entityManager->getRepository(Beats::class)->find($id);
            $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
            $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente])?$this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente]):new Commande();
            if($commande->getOrderBeats() == null){
                $orderBeats = new OrderBeats();
            }else{
                $orderBeats = $commande->getOrderBeats();
            }
            $category = $beats->getCategory();

            return $this->render('beats/details.html.twig', [
                'beats' => $beats,
                'panier' => $orderBeats,
                'categorie' => $category,
            ]);
        }else{
            $beats = $this->entityManager->getRepository(Beats::class)->find($id);
            $category = $beats->getCategory();

            return $this->render('beats/details.html.twig', [
                'beats' => $beats,
                'categorie' => $category,
            ]);
        }
    }
}