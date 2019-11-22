<?php

namespace App\Form;

use App\Entity\RepresentantFamille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RepresentantFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login')
            ->add('motdepasse')
            ->add('nom')
            ->add('prenom')
            ->add('adresse')
            ->add('noMobile')
            ->add('noFixe')
            ->add('mail')
            ->add('dateNaissance')
            ->add('dateFinAdhesion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RepresentantFamille::class,
        ]);
    }
}
