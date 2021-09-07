<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//classe qui n'as pas d'entité, me permettant de construire le form de la recherche
class SearchType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('string', TextType::class, [
            'label' => 'Rechercher',
            'required' => false,
            'attr' => [
                'placeholder' => 'Votre recherche...',
                'class' => 'form-control-sm'
            ]
        ])
        //j'utilise EntityType pour lier un input, une propriéte de mon form à la class Catégory
        ->add('categories', EntityType::class , [
            'label' => false,
            'required' => false,
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true

        ])
        ->add('submit' , SubmitType::class, [
            'label' => 'Filtrer',
            'attr' => [
                'class' => 'btn-block btn-info'
            ] 
        ])

        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        //Relie le formulaire à la classe Search
        $resolver->setDefaults([
            'data_class'=> Search::class,
            'method' => 'GET',
            //on désactive la crsf protection de Symfony
            'crsf_protection' => false,
        ]);
    }
        //me retourne une url clean sans rien
    public function getBlockPrefix()
    {
        return '';
    }

}