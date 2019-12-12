<?php

namespace App\Form;

use App\Entity\InformationsFamille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noAllocataire')
            ->add('nomCAF')
            ->add('nbEnfants')
            ->add('estMonoparentale')
            ->add('regimeProtectionSociale')
            ->add('representant_famille', HiddenType::class, ['required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InformationsFamille::class,
        ]);
    }
}
