<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subsDate', DateTimeType::class)
            ->add('validationState', ChoiceType::class, [
                'choices'  => [
                    'Validé' => true,
                    'Non validé' => false,
                ],
            ])
            ->add('event', EventType::class)
            ->add('user', UserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
