<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeliveryController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/livraison', name: 'app_delivery')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$user, 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        $beats = $orderBeats->getBeats()->findAll();

        $email = (new Email())
        ->from(new Address('musicandbeats@beats.com', 'musicandbeats'))
        ->to($user->getUserIdentifier())
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Vos BEATS')
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');

        return $this->render('delivery/index.html.twig', [
            'controller_name' => 'DeliveryController',
        ]);
    }
}
