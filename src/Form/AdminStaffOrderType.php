<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminStaffOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName')
            ->add('customerEmail')
            ->add('phone')

            // ✅ MENU DROPDOWN
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a menu item',
            ])

            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1],
            ])

            ->add('specialRequest', TextareaType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
