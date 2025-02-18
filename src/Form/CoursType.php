<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Instructeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class) // Title as text input
            ->add('description') // Description as text area
            ->add('image', FileType::class, [
                'required' => false, // Make image optional
                'mapped' => false,  // Don't map this to the Cours entity directly
            ])
            ->add('datePublication', null, [
                'widget' => 'single_text', // A single text field for the date
            ])
            ->add('duree', IntegerType::class) // Duration as integer input
            ->add('prix', MoneyType::class, [
                'currency' => 'TND', // Set the currency to TND (Tunisian Dinar)
            ]) 
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    'Beginner' => 'Beginner',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                ],
            ])
            ->add('instructeur', EntityType::class, [
                'class' => Instructeur::class,
                'choice_label' => 'nom', // Assuming you want to display the name of the instructor
                'placeholder' => 'Choose an instructor', // Optional placeholder
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class, // Bind to the Cours entity
        ]);
    }
}

