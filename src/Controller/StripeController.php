<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager,Cart $cart, $reference): Response
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
       
        //On ajoute une sécurité suppl au cas ou il n'y a pas de commandes 
        if(!$order) {
            new JsonResponse(['error' => 'order']);
        } 

        foreach ($order->getOrderDetails()->getValues() as $product) {
            
        //ici on indique la totalité de la commande
            $product_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            $products_for_stripe[]  = [
                    'price_data' =>[
                        'currency' => 'eur',
                        'unit_amount' => $product->getPrice(),
                        'product_data' => [
                            'name' => $product->getProduct(),
                            'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                        ],
                    ],
                    'quantity' => $product->getQuantity(),
               
            ];
        }
        //on rajoute cette fois ci le prix de la livraison

        $products_for_stripe[]  = [
            'price_data' =>[
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
       
    ];


                //Initialisation de l'api Stripe 
                Stripe::setApiKey('sk_test_51JYyS8CPVjZaB3vvbnvvr13SPWbcj9IWxteArIBSW16T4wWylajRiX6UAHOCkafHljWUs9YVEo3jPqubrP7yp2G400XNk3C340');
                

                $checkout_session = Session::create([
                    'customer_email' => $this->getUser()->getEmail(),
                    'payment_method_types' => ['card'],
                    'line_items' =>  [ 
                        $products_for_stripe
                    ],
                    
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
    
                ]);

                $order->setStripeSessionId($checkout_session->id);
                $entityManager->flush();
              
                
            $response = new JsonResponse(['id' => $checkout_session->id]);
            return $response;

/* 
                header("HTTP/1.1 303 See Other");
    
                header("Location: " . $checkout_session->url);
                      */
    }
    
}
