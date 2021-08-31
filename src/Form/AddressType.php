<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adresse',
                'attr' => [
                    'placeholder' => 'Nommer votre adresse'
                ]
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Entrez votre prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Entrez votre nom',
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('company',TextType::class, [
                'label' => 'Votre société',
                'attr' => [
                    'placeholder' => '(facultatif) Votre nom de société'
                ]
            ])
            ->add('adress',TextType::class, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => 'ex: 8 rue de la martiniere....'
                ]
            ])
            ->add('postal',TextType::class, [
                'label' => 'Votre code postal',
                'attr' => [
                    'placeholder' => 'Entrez votre code postal'
                ]
            ])
            ->add('city',TextType::class, [
                'label' => 'Votre ville',
                'attr' => [
                    'placeholder' => 'Votre ville'
                ]
            ])
            ->add('country',CountryType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Pays'
                ]
            ])
            ->add('phone',TelType::class, [
                'label' => 'Votre téléphone',
                'attr' => [
                    'placeholder' => 'numéro de téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter mon adresse',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
