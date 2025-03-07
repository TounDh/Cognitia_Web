<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Quiz;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;



class QuizType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser(); // Récupérer l'utilisateur connecté
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles()); // Vérifier si c'est un admin

        $builder
            ->add('titre',TextType::class, [
                'required' => false
            ])
            ->add('tempsMax',TextType::class, [
                'required' => false
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'titre',
                'placeholder' => 'Sélectionner un cours',
                'disabled' => true,
                'required' => false
            ])
            ->add('instructeur', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (UserRepository $ur) use ($user, $isAdmin) {
                    $qb = $ur->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_INSTRUCTEUR%');

                    if (!$isAdmin) {
                        // Si ce n'est pas un admin, on affiche seulement l'instructeur connecté
                        $qb->andWhere('u = :user')
                           ->setParameter('user', $user);
                    }

                    return $qb;
                },
                'choice_label' => 'email',
                'placeholder' => 'Sélectionner un instructeur',
                'required' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
