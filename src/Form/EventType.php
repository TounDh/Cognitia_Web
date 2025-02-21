<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de début ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('dateFin', DateTimeType::class, [
            
                'label' => 'Date Fin',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
                
                'constraints' => [
                    new NotBlank(['message' => 'La date de fin est obligatoire.']),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être après la date de début.',
                    ]),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de l’événement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer l\'événement',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}