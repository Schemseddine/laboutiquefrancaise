<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountPasswordController extends AbstractController
{

private $entityManager;

public function __construct(EntityManagerInterface $entityManager){

    $this->entityManager = $entityManager;
    
}

    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $hash): Response
    {
        $notification = null;
        $user = $this->getUser();
        
        $form = $this->createForm(ChangePasswordType::class, $user);
    

        $form->handleRequest($request);
      

        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();

            if($hash->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
              
                $password = $hash->hashPassword($user, $new_pwd);

                $user->setPassword($password);
                //doctrine mets à jous les données avec flush
                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour.';

            } else {
                $notification = 'Votre mot de passe est incorrect';
            }

        }

        return $this->render('account/password.html.twig', [
            'form'=> $form->createView(),
            'notification'=> $notification

        ]);
    }
}
