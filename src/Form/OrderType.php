<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('addresses' , EntityType::class , [
                'label' => 'Choisissez votre adresse de livraison',
                'required' => true,
                'class' => Adress::class,
                'choices' => $user->getAdresses(),
                'multiple' => false,
                'expanded' =>true
            ])

            ->add('carriers' , EntityType::class , [
                'label' => 'Choisissez votre transporteur',
                'required' => true,
                'class' => Carrier::class,
                'multiple' => false,
                'expanded' =>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => array()// Configure your form options here''
        ]);
    }
}