<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais');
            //On envoi un mail
/*             $content = $form->getData();

            $mail = new Mail();
            $mail->send('schemseddinelarbi@orange.fr','l\'evidence', 'Vous avez reçu une nouvelle demande de contact',$content);
 */



        }


        return $this->render('contact/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
