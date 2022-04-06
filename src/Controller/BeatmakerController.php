<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BeatmakerController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/artiste', name: 'app_beatmaker')]
    public function index(UserRepository $userRepository): Response
    {
        $beats = [];
        $beatmakers = $userRepository->findInRoles("ROLE_BEATMAKER");
        foreach ($beatmakers as $beatmaker) {
            $beats[$beatmaker->getId()] = $beatmaker->getBeats();
        }
        
        return $this->render('beatmaker/index.html.twig', [
            'beatmakers' => $beatmakers,
            'beats' => $beats,
        ]);
    }
    
}