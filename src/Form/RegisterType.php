<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=>'votre prénom',
                'constraints' => new Length([
                    'min'=> 2,
                    'max'=> 20
                ]),
                'attr'=> [
                    'placeholder'=> 'Merci de saisir votre prénom'
                ]

            ])
            ->add('lastname', TextType::class, [
                'label'=>'votre nom',
                'constraints' => new Length([
                    'min'=> 2,
                    'max'=> 20
                ]),
                'attr'=> [
                    'placeholder'=> 'Merci de saisir votre nom'
                ]
            ])

            ->add('email', EmailType::class,[
                'label'=> 'Votre Email',
                'constraints' => new Length([
                    'min'=> 5,
                    'max'=> 20
                ]),
                'attr'=> [
                    'placeholder'=> 'Merci de saisir votre Email'
                ]
            ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message'=> 'le mot de passe et la confirmation doivent etre identiques',
                'label'=>'Votre mot de passe',
                'required'=> true,
                'first_options'=> [
                    'label'=> 'Mot de passe',
                    'attr'=> [
                        'placeholder'=>'Merci de saisir votre mot de passe'
                            ]
                    ],
                
                'second_options'=> ['label'=> 'Confirmez votre mot de passe',
                    'attr'=> [
                        'placeholder'=>'Merci de confirmer votre mot de passe'
                    ]
                ],
            
            ])

            ->add('submit', SubmitType::class, [
                'label'=>"S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> User::class,
        ]);
    }
}
