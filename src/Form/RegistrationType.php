<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', null, [
            'label' => 'Prénom',
        ])
        ->add('lastName', null, [
            'label' => 'Nom de famille',
        ])
        ->add('email', null, [
            'label' => 'Adresse email',
        ])
        ->add('birthDate', BirthdayType::class, [
            'label' => 'Date de naissance',
            'format' => 'dd MM yyyy',
        ])
        ->add('phone', null, [
            'label' => 'Numéro de téléphone',
        ])
        ->add('licenceNumber', null, [
            'label' => 'Numéro de licence',
            'help' => 'Il n\'est pas obligatoire de renseigner votre numéro de licence lors de la création de votre compte. En revanche il sera indispensable pour s\'insrire aux évènements.',
        ])
        ->add('roles', HiddenType::class, [
            'empty_data' => ["ROLE_USER"],
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les deux mots de passe saisis ne sont pas identiques.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmation du mot de passe'],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
