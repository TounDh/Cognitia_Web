<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $question = $options['question']; // Récupération du question passé en option

        $builder
            ->add('contenu',TextType::class, [
                'required' => false
            ])
            ->add('estCorrecte')
            ->add('question', EntityType::class, [
                'class' => Question::class,
                'choices' => [$question], // On affiche uniquement le question associé
                'choice_label' => 'contenu',
                'disabled' => true, // Empêche la modification
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
            'question' => null, // Option obligatoire pour passer le question spécifique
        ]);
    }
}
