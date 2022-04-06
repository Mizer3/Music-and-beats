<?php


// use Stripe\Stripe;
// use App\Entity\Commande;
// use App\Entity\OrderBeats;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;

// require 'vendor/autoload.php';

// $stripe = new Stripe();

// $stripe->add(function ($request, $response, $next) {
//     $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
//     \Stripe\Stripe::setApiKey($this->privateKey);
//     return $next($request, $response);
// });

// $stripe->post('/create-checkout-session', function (Request $request, Response $response, OrderBeats $orderBeats) {
//     $session = \Stripe\Checkout\Session::create([
//     'line_items' => [[
//         'price_data' => [
//         'currency' => 'eur',
//         'product_data' => [
//             'name' => $orderBeats->getBeats()->getName(),
//         ],
//         'unit_amount' => $orderBeats->getPrice(),
//         ],
//         'quantity' => 1,
//     ]],
//     'mode' => 'payment',
//     'success_url' => 'https://example.com/success',
//     'cancel_url' => 'https://example.com/cancel',
//     ]);

//     return $response->withHeader('Location', $session->url)->withStatus(303);
// });
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