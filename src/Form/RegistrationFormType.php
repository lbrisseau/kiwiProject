<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
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
            ->add('email')
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('phone', null, [
                'label' => 'Numéro de téléphone',
            ])
            ->add('licenceNumber', null, [
                'label' => 'Numéro de licence',
                'help' => 'Il n\'est pas obligatoire de renseigner votre numéro de licence lors de la création de votre compte. En revanche il sera indispensable pour s\'insrire aux évènements.',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions générales d\'utilisation d\'Auribail MX Park.',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos CGU pour vous inscrire sur le site.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe saisis ne sont pas identiques.',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir un minimum de {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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

