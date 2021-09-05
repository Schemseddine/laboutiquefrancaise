<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
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
}
