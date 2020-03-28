<?php

namespace App\Form;

use App\Entity\RepresentantFamille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class RepresentantFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mail')
            ->add('motDePasse', PasswordType::class,['label' => 'Votre mot de passe', "mapped"=>false])
            ->add('confirmermdp', PasswordType::class,['label' => 'Confirmez votre mot de passe', "mapped"=>false])
            ->add('nom')
            ->add('prenom')
            ->add('ville')
            ->add('codePostal',NumberType::class)
            ->add('adresse')
            ->add('noFixe',  TelType::class,
                [ 'required' => false,
                    'attr' => ['maxlength' => 10]])
            ->add('noMobile', TelType::class,
                [ 'attr' => ['maxlength' => 10]])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'html5' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RepresentantFamille::class,
        ]);
    }
}
