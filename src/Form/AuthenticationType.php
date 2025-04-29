<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthenticationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employee_email',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employee_email',
                    'name' => 'employee[email]',
                    'maxlength' => 255,
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'reuired' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'required',
                        'for' => 'employee_password',
                    ],
                    'attr' => [
                        'id' => 'employee_password',
                        'name' => 'employee[password]',
                        'maxlength' => 255,
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'label_attr' => [
                        'class' => 'required',
                        'for' => 'employee_password_confirmation',
                    ],
                    'attr' => [
                        'id' => 'employee_password_confirmation',
                        'name' => 'employee[password_confirmation]',
                        'maxlength' => 255,
                    ]
                ]                    
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employee_nom',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employee_nom',
                    'name' => 'employee[nom]',
                    'maxlength' => 255,
                ]
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employee_prenom',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employee_prenom',
                    'name' => 'employee[prenom]',
                    'maxlength' => 255,   
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
