<?php

namespace App\Form;

use App\Entity\InformationMajeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoMajeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mail', TextType::class)
            ->add('communicationResponsableLegal', ChoiceType::class, [
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],])
            ->add('membre_famille', HiddenType::class)

            ->add('submit', SubmitType::class)
            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InformationMajeur::class,
        ]);
    }
}
