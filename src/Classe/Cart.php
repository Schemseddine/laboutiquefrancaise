<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart 
{

    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session , EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager; 
    }
    

    public function add($id)
    {

        //Je stocke mon panier avec une session dans la variable $cart grace à la méthode get()
        $cart = $this->session->get('cart', []);

        //incrémantation du produit si l'utilisateur prends plusieurs qtés
        if(!empty($cart[$id])) {

            $cart[$id]++;

        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart );
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    //supprime l'ensemble de mon panier
    public function remove()
    {
        return $this->session->remove('cart');
    }
    
    //supprime un produit de mon panier
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
    //renvoi le nouveau panier sans celui supprimé
        return $this->session->set('cart', $cart);
    }

    public function decrease($id) 
    {

        $cart = $this->session->get('cart', []);
            //vérifier si la qté de notre produit = 1
        if($cart[$id] > 1) {
            // retirer une quantité
            $cart[$id]--;

        }else{
            //Supprime le produit si cart = 1
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    public function getFull()
    {
        $cartComplete = [];

        if($this->get()){
            foreach($this->get() as $id => $quantity){
                $product_object = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

                if(!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }
}