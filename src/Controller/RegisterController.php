<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\RegisterType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{ 
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {

        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/inscription", name="register")
     */

    public function index(Request $request,UserPasswordEncoderInterface $encoder): Response

    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user =$form->getData();

            //Vérifier que l'UI n'est pas déjà inscrit
            $search_mail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if(!$search_mail) {

            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $mail = new Mail;
            $content = "Bonjour" .$user->getFirstname(). "<br/>Venez decouvrir les nouveautés de l'Evidence.<br><br/>Lorem ipsum .... ";
            $mail->send($user->getEmail(), $user->getFirstname(), "Bienvenue sur le site de L'Evidence", $content );


            $notification = "Votre inscription s'est correctement déroulée, vous pouvez dès à présent vous connecter à votre compte";

            } else {

            $notification = "Ce mail existe déjà, veuillez en choisir un autre";
            }


           
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
