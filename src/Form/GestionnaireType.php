<?php

namespace App\Form;

use App\Entity\Gestionnaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('mail')
            ->add('motDePasse', PasswordType::class,['label' => 'Mot de passe : ', "mapped"=>false])
            ->add('confimerMDP', PasswordType::class,['label' => 'Confirmez le mot de passe', "mapped"=>false])
            ->add('idGoogleAuth')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gestionnaires::class,
        ]);
    }
}
