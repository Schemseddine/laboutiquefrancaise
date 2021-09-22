<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;  
    }


    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {

        if($this->getUser()) {

            return $this->redirectToRoute('home');
        }
        //'email' comme paramètre "name" dans la vue Twig ResetPassword
        if($request->get('email')) {
        //On vérifie que l'UI possède un mail dans la bdd
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
           
            if($user) {
                // 1: Enregistrer en bdd la demande de reset_password avec user, token, createtAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user)
                               ->setToken(uniqid())
                               ->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //2 : Envoyer un mail à l'UI avec un lien lui permettant de mettre à jour son password

                $url = $this->generateUrl('update_password',[
                   'token' =>  $reset_password->getToken()
                   ]);

                $content = "Bonjour ".$user->getFirstname()."<br/>Vous avez demandé à réinitialisé votre mot de passe sur le site L'évidence.<br/><br/>";
                $content .="Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'> mettre à jour votre mot de passe</a>.";
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().''.$user->getLastname(), "Réinitialiser votre mot de passe sur votre site L'évidence", $content);
                $this->addFlash('notice', 'Vous aller recevoir dans quelques secondes un mail avec la procédure pour réinitialiser votre mot de passe');

            
            }else{
                $this->addFlash('notice', 'Cette adresse Email est inconnue');

            }
        }
        
        return $this->render('reset_password/index.html.twig', );
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordHasherInterface $hash)
    {
       $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

       if(!$reset_password) {
           return $this->redirectToRoute('reset_password');
       }

       //Vérifier si le creatdAt = now -3h
       $now = new DateTime();
       if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')) {
          //Modifier mon mot de passe
          $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveller');

          return $this->redirectToRoute('reset_password');
       }
       //Rendre une vue avec MDP et confirmation de MDP
       $form = $this->createForm(ResetPasswordType::class);
       $form->handleRequest($request);
        
       if($form->isSubmitted() && $form->isValid()) {
       $new_pwd = $form->get('new_password')->getData();
        //Encodage des mots de passe
       $password = $hash->hashPassword($reset_password->getUser(), $new_pwd);
       $reset_password->getUser()->setPassword($password);

          //Flush en bdd
        $this->entityManager->flush();
       
    
       //Redirection de l'UI vers la page de connexion
       $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
       return $this->redirectToRoute('app_login');

       }

       return $this->render('reset_password/update.html.twig',[
           'form' => $form->createView()
       ] );
 
    }
}
