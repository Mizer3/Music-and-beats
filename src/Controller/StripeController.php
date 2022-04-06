<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Commande;
use App\Entity\OrderBeats;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{   
    private $privateKey;

    public function __construct()
    {
        if($_ENV['APP_ENV'] === 'dev'){
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        }
    }
    #[Route('/panier/create-session/{id}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, Request $request, $id)
    {
        // $id = $this->entityManager->getRepository(Commande::class)->getId();
        // $statut_attente = $this->entityManager->getRepository(Statut::class)->findOneByName('En attente');
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $commande = $entityManager->getRepository(Commande::class)->findOneBy(['id'=>$id]);

        // if(!$commande){
        //     new JsonResponse(['error' => 'order']);
        // }

        foreach($commande->getOrderBeats() as $beats){
            $products_for_stripe = [
                'price_data' => [
                    'currency' => Commande::DEVISE,
                    'unit_amount' => $beats->getPrice() * 100],
                'product_data' => [
                    'name' => $beats->getBeats()->getName(),
                        'images' => $beats->getBeats()->getImageName(),
                    ],
                    
                
                'quantity' => 1,
            ];
        }
        // dd($products_for_stripe);
        Stripe::setApiKey($this->privateKey);

        $checkout_session = Session::create([
            'customer_email'=> $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $products_for_stripe,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/panier/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/panier/erreur/{CHECKOUT_SESSION_ID}',            
        ]);

        $commande->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
// class StripeService{

//     private $privateKey;

//     public function __construct()
//     {
//         if($_ENV['APP_ENV'] === 'dev'){
//             $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
//         }
//     }

//     public function paymentIntent(Commande $commande){


//         \Stripe\Stripe::setApiKey($this->privateKey);

//         return \Stripe\PaymentIntent::create([
//             'amount' => $commande->getOrderBeats()->getPrice() * 100,
//             'currency' => Commande::DEVISE,
//             'payment_method_type' => ['card',],
//         ]);
//     }

//     public function paiement($amount, $currency, $beats, array $stripeParameter){

//         \Stripe\Stripe::setApiKey($this->privateKey);
//         $payment_intent = null;

//         if(isset($stripeParameter['stripeIntentId'])){

//             $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
//         }

//         if($stripeParameter['stripeIntentId'] === 'succeeded'){

//             //TODO
//         }else{
//             $payment_intent->cancel();
//         }

//         return $payment_intent;
//     }

//     public function stripe(array $stripeParameter, Commande $commande){

//         $totalPrix = 0;
//         $name = "";
//         foreach ($commande->getOrderBeats() as $key => $orderBeat) {
//             $totalPrix+=$orderBeat->getPrice();
//             $name+=$orderBeat->getBeats()->getName()." / ";
//         }
//         return $this->paiement(
//             $totalPrix * 100,
//             Commande::DEVISE,
//             $name,
//             $stripeParameter
//         );
//     }
// }