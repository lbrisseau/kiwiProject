<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultStartMemberSubs', IntegerType::class, [
                'label' => 'Inscription Membres : Nombre de jours entre entrainement et ouverture des inscriptions',
                'attr' => [
                    'placeholder' => 'Nombre de jours entre entrainement et ouverture des inscriptions',
                ],
            ])
            ->add('defaultStartAllSubs', IntegerType::class, [
                'label' => 'Inscription (autres) : Nombre de jours entre entrainement et ouverture des inscriptions',
                'attr' => [
                    'placeholder' => 'Nombre de jours entre entrainement et ouverture des inscriptions',
                ],
            ])
            ->add('defaultEndSubs', IntegerType::class, [
                'label' => 'Fermeture Inscriptions : Nombre de jours entre entrainement et fermeture des inscriptions',
                'attr' => [
                    'placeholder' => 'Nombre de jours entre entrainement et fermeture des inscriptions',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
