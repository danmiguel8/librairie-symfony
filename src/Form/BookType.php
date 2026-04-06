<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
                'attr' => [
                    'placeholder' => 'Ex : Le Seigneur des Anneaux',
                    'class' => 'form-control mb-2'
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Résumé ou présentation du livre…',
                    'rows' => 3,
                    'class' => 'form-control mb-2'
                ],
            ])

            ->add('author', EntityType::class, [
                'label' => 'Auteur',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'attr' => ['class' => 'form-select mb-2'],
            ])

            ->add('category', EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select mb-2'],
            ])

            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => [
                    'placeholder' => 'Ex : 12',
                    'min' => 0,
                    'class' => 'form-control mb-2'
                ],
            ])

            ->add('file', FileType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
