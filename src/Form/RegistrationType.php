<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', null, ['label' => 'Prénom'])
        ->add('lastName', null, ['label' => 'Nom de famille'])
        ->add('email', null, ['label' => 'Adresse email'])
        ->add('birthDate', BirthdayType::class, ['label' => 'Date de naissance', 'format' => 'dd MM yyyy'])
        ->add('phone', null, ['label' => 'Numéro de téléphone'])
        ->add('licenceNumber', null, ['label' => 'Numéro de licence'])
        ->add('roles', HiddenType::class, ['empty_data' => '["ROLE_USER"]'])
        ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
        ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
