<?php

namespace App\Form;

use App\Entity\Animation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('carouselMessage1', TextType::class, [
                'label' => 'Message n°1',
                'attr' => [
                    'placeholder' => 'Entrez ici le texte qui s\'affichera dans le carroussel',
                ],
            ])
            ->add('carouselMessage2', TextType::class, [
                'label' => 'Message n°2',
                'attr' => [
                    'placeholder' => 'Entrez ici le texte qui s\'affichera dans le carroussel',
                ],
            ])
            ->add('carouselMessage3', TextType::class, [
                'label' => 'Message n°3',
                'attr' => [
                    'placeholder' => 'Entrez ici le texte qui s\'affichera dans le carroussel',
                ],
            ])
            ->add('carouselMessage4', TextType::class, [
                'label' => 'Message n°4',
                'attr' => [
                    'placeholder' => 'Entrez ici le texte qui s\'affichera dans le carroussel',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Animation::class,
        ]);
    }
}
