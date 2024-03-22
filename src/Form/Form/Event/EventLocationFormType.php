<?php

namespace App\Form\Form\Event;

use App\DataTransferObject\EventDetailsDto;
use App\DataTransferObject\EventLocationDto;
use App\Entity\Event\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventLocationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('longitude', TextType::class)
            ->add('latitude', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventLocationDto::class,
        ]);
    }
}
