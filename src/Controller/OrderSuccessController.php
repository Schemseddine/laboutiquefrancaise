<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
      // On rajoute une sécu supplémentaire
        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
       

        if($order->getState() == 0 ) {
        //Vider la session "cart"
        $cart->remove();
        
        //Modifier le statut isPaid() de notre commande en mettant 1 
        $order->setState(1);
        $this->entityManager->flush();
        }

        //Envoyer un email de confirmation de commande à notre client

        $mail = new Mail;
        $content = "Bonjour ".$order->getUser()->getFirstname(). "<br/> Merci pour votre commande.<br/><br/>Lorem ipsum .... ";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande L'évidence est bien validée", $content );


        //Afficher les quelques informations de la commande de l'utilisateur

        return $this->render('order_success/index.html.twig', [

            'order' => $order,
        ]);
    }
}
