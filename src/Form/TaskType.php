<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project_id = $options['project_id'];
        
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tache',
                'label_attr' => [
                    'class' => 'required',
                    'for' => 'tache_titre',
                ],
                'attr' => [
                    'required' => true,
                    'id' => 'tache_titre',
                    'name' => 'tache[titre]',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => [
                    'for' => 'tache_description',
                ],
                'attr' => [
                    'id' => 'tache_description',
                    'name' => 'taache[description]',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'label_attr' => [
                    'for' => 'tache_deadline',
                ],
                'attr' => [
                    'id' => 'tache_deadline',
                    'name' => 'tache[deadline]',
                ],
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
                'label' => 'Statut',
                'label_attr' => [
                    'for' => 'tache_statut',
                ],
                'attr' => [
                    'id' => 'tache_statut',
                    'name' => 'tache[statut]',
                ],
                'choice_label' => function(TaskStatus $status) {
                    return strtoupper($status->value);
                },
                'multiple' => false,
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'label' => 'Membre',
                'label_attr' => [
                    'for' => 'tache_employe',
                ],
                'attr' => [
                    'id' => 'tache_employe',
                    'name' => 'tache[employe]',
                ],
                'query_builder' => function(EntityRepository $er) use ($project_id): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->select('e')
                        ->join('e.projects', 'p')
                        ->where('p.id = :id')
                        ->setParameter('id', $project_id);
                },
                'choice_label' => 'name',
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project_id' => null,
        ]);
    }
}
