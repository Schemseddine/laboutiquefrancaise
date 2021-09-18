<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
     $this->entityManager = $entityManager;   
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
        // On rajoute une sécu supplémentaire
          if(!$order || $order->getUser() != $this->getUser()) {
              return $this->redirectToRoute('home');
          }
          //Envoyer un mail à notre utilisateur pour lui indiquer l'échec de paiment


        return $this->render('order_cancel/index.html.twig',[
            'order' => $order
        ]);
    }
}
