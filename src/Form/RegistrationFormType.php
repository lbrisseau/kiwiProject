<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
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
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions générales d\'utilisation.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les CGU.',
                    ]),
                ],
            ])
            // ->add('plainPassword', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     // 'required' => true,
            //     'mapped' => false,
            //     'invalid_message' => 'Les mots de passe saisis ne sont pas identiques.',
            //     'options' => ['attr' => ['class' => 'password-field']],
            //     'first_options'  => ['label' => 'Mot de passe'],
            //     'second_options' => ['label' => 'Confirmation du mot de passe'],
            //     'constraints' => [
            //         new NotBlank(),
            //         new Length(['min' => 8])
    
            //     ],
            // ])
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
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
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
