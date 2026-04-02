<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message : 'Veuillez entrer votre prénom.'),
                ],
                'attr' => [
                    'placeholder' => 'Votre prénom'                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message : 'Veuillez entrer votre nom.'),
                ],
                'attr' => [
                    'placeholder' => 'Votre nom',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message : 'Veuillez entrer votre email.'),
                    new Assert\Email(message : 'Adresse email invalide.')
                ],
                'attr' => [
                    'placeholder' => 'exemple@mail.com',
                    'required' => false
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message : 'Veuillez entrer un mot de passe.'),
                    new Assert\Length(
                        min : 6,
                        minMessage : 'Le mot de passe doit contenir au moins 6 caractères.'
                    )
                ],
                'attr' => [
                    'placeholder' => 'Votre mot de passe'
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(message: "Veuillez confirmer votre mot de passe."),
                    new Assert\Expression(
                        expression: 'this.getParent().get("password").getData() === value',
                        message: "Les mots de passe ne correspondent pas."
                    )
                ],
                'attr' => [
                    'placeholder' => 'Répétez le mot de passe',
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
