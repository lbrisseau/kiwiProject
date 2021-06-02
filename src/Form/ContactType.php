<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                // 'attr' => [
                //     'placeholder' => 'Votre prénom',
                // ],
                // 'attr' => [
                //     'oninvalid' => "setCustomValidity('Par quel doux prénom vous faites vous appeler ?')"
                // ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                // 'attr' => [
                //     'placeholder' => 'Votre nom de famille',
                // ],
                // 'attr' => [
                //     'oninvalid' => "setCustomValidity('Quel est votre petit nom ?')"
                // ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                // 'attr' => [
                //     'placeholder' => 'Votre adresse email',
                // ],
                // 'attr' => [
                //     'oninvalid' => "setCustomValidity('J'ai besoin de votre adresse email !')"
                // ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Objet',
                // 'attr' => [
                //     'placeholder' => 'Objet du message',
                // ],
                // 'attr' => [
                //     'oninvalid' => "setCustomValidity('Pourquoi souhaitez-vous nous contacter ?')"
                // ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                // 'attr' => [
                //     'placeholder' => 'Votre message',
                // ],
                // 'attr' => [
                //     'oninvalid' => "setCustomValidity('La concision est une qualité, mais là il y a de l'abus.')"
                // ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
