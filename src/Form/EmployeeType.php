<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Enum\EmployeeStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employe_nom',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employe_nom',
                    'name' => 'employe[nom]',
                    'maxlength' => 255,
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employe_prenom',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employe_prenom',
                    'name' => 'employe[prenom]',
                    'maxlength' => 255,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employe_email',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employe_email',
                    'name' => 'employe[email]',
                    'maxlength' => 255,
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date d\'entrée',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employe_dateArrivee',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employe_dateArrivee',
                    'name' => 'employe[dateArrivee]',
                ],
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('status', EnumType::class, [
                'class' => EmployeeStatus::class,
                'label' => 'Statut',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'employe_statut',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'employe_statut',
                    'name' => 'employe[statut]',
                ],
                'choice_label' => function(EmployeeStatus $status) {
                    return $status->value;
                },
                'multiple' => false,
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
