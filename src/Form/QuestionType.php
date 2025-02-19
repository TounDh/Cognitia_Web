<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $quiz = $options['quiz']; // Récupération du quiz passé en option

        $builder
            ->add('contenu',TextType::class, [
                'required' => false
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choices' => [$quiz], // On affiche uniquement le quiz associé
                'choice_label' => 'titre',
                'disabled' => true, // Empêche la modification
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'quiz' => null, // Option obligatoire pour passer le quiz spécifique
        ]);
    }
}
