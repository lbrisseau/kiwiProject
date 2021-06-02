<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
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
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                // 'attr' => [
                //     'placeholder' => 'Votre nom de famille',
                // ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                // 'attr' => [
                //     'placeholder' => 'Votre adresse email',
                // ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Objet',
                // 'attr' => [
                //     'placeholder' => 'Objet du message',
                // ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                // 'attr' => [
                //     'placeholder' => 'Votre message',
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
