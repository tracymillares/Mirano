<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Meal' => 'Meal',
                    'Snack' => 'Snack',
                    'Dessert' => 'Dessert',
                    'Drink' => 'Drink',
                    'Appetizer' => 'Appetizer',
                ],
                'placeholder' => 'Select a category',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                               focus:border-rose-500 focus:ring-rose-500 sm:text-sm',
                ],
            ])
            ->add('price')
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Write a short description of the dish...',
                    'rows' => 4,
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Food Image (JPEG or PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
