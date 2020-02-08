<?php

namespace App\Form;

use App\Entity\Dispositions;
use App\Entity\Droits;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DispositionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // MARCHE POUR 1 AJOUT UNIQUEMENT

            ->add('droits', EntityType::class, [
                'label'=>'droits',
                'class' => Droits::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
            ]);

/*            ->add('droits', ChoiceType::class, [
                'choices' => [
                    'Consulter un compte adhérent',
                    'Créer un compte adhérent',
                    'Modifier un compte adhérent',
                    'Supprimer un compte adhérent',
                    'Consulter les dossiers des adhérents',
                    'Créer les dossiers des adhérents',
                    'Modifier les dossiers des adhérents',
                    'Supprimer les dossiers des adhérent',
                    'Exporter les infos des adhérents'
                ],
                'expanded' => true,
                //'multiple' => true,
            ]);*/


/*            ->add('droits', CollectionType::class, [
                'entry_type' => DroitsType::class,
            ]);*/


            /*
            ->add('droits', EntityType::class, [

                'class' => 'App\Entity\Droits',
                'choices' => DroitsType::class,

                'class' => Dispositions::class,
                'choice_label' => 'droits.libelle',
                'multiple' => true,
                'expanded' => true,

            ]);*/
/*
        $formModifier = function (FormInterface $form, Droits $droits = null)
        {
            $form
                ->add('droits', EntityType::class, [
                    'class' => 'App\Entity\Droits',
                    'choices' => $droits->getLibelle()
                ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getDroits());
            }
        );

        $builder->get('droits')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $droits = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $droits);
            }
        );*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dispositions::class,
        ]);
    }
}
