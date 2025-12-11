<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReservationBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Full Name',
                'attr' => ['placeholder' => 'Your Name']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'you@example.com']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone Number',
                'attr' => ['placeholder' => '09xxxxxxxxx']
            ])
            ->add('guest', IntegerType::class, [
                'label' => 'Number of Guests',
                'attr' => ['min' => 1]
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Reservation Date & Time',
            ])
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => 'name',
                'placeholder' => 'Search or select a dish (optional)',
                'required' => false,
                'attr' => [
                    'class' => 'menu-search', // for JS search enhancement
                    'data-controller' => 'tom-select', // optional if you use JS autocomplete
                   
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Additional Notes',
                'required' => false,
                'attr' => ['placeholder' => 'Any special requests?']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
