<?php

namespace App\Form;

use App\Entity\InformationsFamille;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noAllocataire', TextType::class, ['required' => false])
            ->add('nomCAF', TextType::class, ['required' => false])
            ->add('nbEnfants', TextType::class, ['required' => false])
            ->add('estMonoparentale')
            ->add('regimeProtectionSociale', TextType::class, ['required' => false])
            //->add('representant_famille', HiddenType::class, ['required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InformationsFamille::class,
        ]);
    }
}
