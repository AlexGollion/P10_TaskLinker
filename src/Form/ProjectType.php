<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du projet',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'projet_name',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'projet_name',
                    'name' => 'projet[name]',
                ]
            ])
            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'label' => 'Inviter des membres',
                'label_attr' => [
                    'for' => 'projet_employes',
                ],
                'attr' => [
                    'id' => 'projet_employes',
                    'name' => 'projet[employes][]',
                ],
                'choice_label' => function(Employee $employee) {
                    return mb_substr($employee->getFirstName(), 0, 1) . '' . mb_substr($employee->getName(), 0, 1);
                },
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }

}
