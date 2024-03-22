<?php

namespace App\Form\Form\Event;

use App\DataTransferObject\EventDetailsDto;
use App\DataTransferObject\EventInviationsDto;
use App\DataTransferObject\EventLocationDto;
use App\Entity\Event\Event;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventInviationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'expanded' => true,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventInviationsDto::class,
        ]);
    }
}
