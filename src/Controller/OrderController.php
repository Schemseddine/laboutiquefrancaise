<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{


    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart): Response
    {

        //si l'utilisateur n'a pas d'adresses enregistrée
        if(!$this->getUser()->getAdresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        // null en 2° param de la methode createForm car elle n'est pas reliée à une classe
        //Je lui passe la variable 'user' pour n'avoir que les adresses de l'utilisateur en cours
        $form = $this->createForm((OrderType::class), null , [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
           
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm((OrderType::class), null , [
            'user' => $this->getUser()
        ]);

        //validation du panier

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() )
        {
         
            $date = new DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().''.$delivery->getLastname();
            $delivery_content .= '<br/>' .$delivery->getPhone();
           

            if($delivery->getCompany()) 
            {
            $delivery_content .= '<br/>' .$delivery->getCompany();
            }
            $delivery_content .= '<br/>' .$delivery->getAdress();
            $delivery_content .= '<br/>' .$delivery->getPostal().''.$delivery->getCity();
            $delivery_content .= '<br/>' .$delivery->getCountry();

            //Enregistrer ma commande Order()

            $order = new Order();
            $reference = $date->format('dmy').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);

            //Enregister mon produit OrderDetails()

            foreach($cart->getFull() as $product) {
          
            $orderDetails = new OrderDetails;
            $orderDetails->setMyOrder($order);
            $orderDetails->setProduct($product['product']->getName());
            $orderDetails->setQuantity($product['quantity']);
            $orderDetails->setPrice($product['product']->getPrice());
            $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
            $this->entityManager->persist($orderDetails);

            }

               
            $this->entityManager->flush();


            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
               
            ]);
        }
       return $this->redirectToRoute('cart');
    }
}
