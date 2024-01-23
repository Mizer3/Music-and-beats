<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Beats;
use App\Entity\Statut;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Entity\OrderBeats;
use App\Form\ValiderPanierType;
use App\Stripe\StripeService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class PanierController extends AbstractController
{
    protected $stripeService;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/panier', name: 'panier')]
    public function index(): Response
    {
        if($this->getUser()){
            $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=> 2,]);
            $orderBeats = $currentCommande->getOrderBeats();

            return $this->render('panier/index.html.twig', [
                'orderBeats' => $orderBeats,
                'id' => $currentCommande?$currentCommande->getId():null,
            ]);
        }else{
            return $this->render('panier/index.html.twig', [
                'commandes' => null,
            ]);
        }
    }
    
    #[Route('/ajoutPanier', name: 'ajoutPanier')]
    public function ajoutPanier(Request $request)
    {
        if ($this->getUser()) {
            $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
            $commande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente])?$this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$this->getUser(), 'statut'=>$statut_attente]):new Commande();
            $beats = $this->entityManager->getRepository(Beats::class)->find($request->request->get('id'));
            if($commande->getOrderBeats() == null){
            $orderBeats = new OrderBeats();
            }else{
                $orderBeats = $commande->getOrderBeats();
            }
            $orderBeats->addBeat($beats);
            $totalBeats = $orderBeats->getTotalPrice();
            if($totalBeats == null){
                $totalBeats = 0;
            }
            $price = $beats->getPrice();
            $totalPrice = $totalBeats + $price;
            $orderBeats->setTotalPrice($totalPrice);

            $commande->setUser($this->getUser());
            if ($commande->getCreatedAt()) {
                $commande->setUpdatedAt(new DateTimeImmutable());
            } else {
                $commande->setCreatedAt(new DateTimeImmutable());
                $commande->setUpdatedAt(new DateTimeImmutable());
            }
            $commande->setStatut($statut_attente);
            $commande->setOrderBeats($orderBeats);
            $this->entityManager->persist($orderBeats);
            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            return new JsonResponse($request->request->get('id'));
        }else{
            return new JsonResponse('toto');
        }
        
    }
    #[Route('/suppPanier/{beats}/{orderBeats}', name: 'suppPanier')]
    public function suppPanier(Beats $beats ,OrderBeats $orderBeats): Response
    {
        $id = $beats->getId();
        $orderBeats->removeBeat($beats);
        $price = $beats->getPrice();
        $totalBeats = $orderBeats->getTotalPrice();
        $totalPrice = $totalBeats - $price;
        $orderBeats->setTotalPrice($totalPrice);
        $this->entityManager->persist($orderBeats);
        $this->entityManager->flush();

        return $this->json(['id' => $id ]);
    }

    #[Route('/paiement-accepté', name: 'app_delivery')]
    public function validerPanier(MailerInterface $mailer): Response
    {

        $user = $this->getUser();
        $currentCommande = $this->entityManager->getRepository(Commande::class)->findOneBy(['user'=>$user, 'statut'=> 2,]);
        $orderBeats = $currentCommande->getOrderBeats();
        // $beats = $orderBeats->getBeats()->findAll();

        $email = (new TemplatedEmail())

            ->from(new Address('musicandbeats@beats.com', 'musicandbeats'))
            ->to($user->getUserIdentifier())
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Vos BEATS')
            // ->attachFromPathforeach ($beats as $beat) {
            //     ('/path/to/uploads/to/beats/' ~ {{beat.name}});
            // })
            ->attachFromPath('/path/to/beats/RAIN.mp3')
            ->htmlTemplate('delivery/mail.html.twig')
            ->context([
                'user' => $user,
            ]);

        $mailer->send($email);

        return $this->render('delivery/index.html.twig', [
            'user' => $user,
        ]);
    }

    // https://www.youtube.com/watch?v=bG5PYy6tp60

    // public function intentSecret(Commande $commande){

    //     $intent = $this->stripeService->paymentIntent($commande);

    //     return $intent['client_secret'] ?? null;
    // }

    // public function stripe(array $stripeParameter, Commande $commande)
    // {
    //     $ressource = null;
    //     $data = $this->stripeService->stripe($stripeParameter, $commande);

    //     if($data){
    //         $ressource = [
    //             'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['crad']['brand'],
    //             'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['crad']['last4'],
    //             'stripeId' => $data['charges']['data'][0]['id'],
    //             'stripeStatus' => $data['charges']['data'][0]['status'],
    //             'stripeToken' => $data['client_secret'],
    //         ];
    //     }

    //     return $ressource;
    // }

    // public function create_subscription(array $ressource, Beats $beats, User $user){

    //     $order = new Order();
    //     $order->setUser($user);
    //     $order->setBeats($beats);
    //     $order->setPrice($beats->getPrice());
    //     $order->setReference( uniqid('', false));
        
    // }
}
