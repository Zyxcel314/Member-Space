<?php

namespace App\Form;

use App\Entity\Dispositions;
use App\Entity\Droits;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DroitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*            ->add('libelle', ChoiceType::class, [
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
                ]
            ])*/

                ->add('libelle');

                //->add('libelle');

/*            ->add('droits', EntityType::class, array(
                'class' => Droits::class,
                'choice_label' => 'libelle',
                'expanded' => true,
                'multiple' => true,
            ))*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Droits::class,
        ]);
    }
}
