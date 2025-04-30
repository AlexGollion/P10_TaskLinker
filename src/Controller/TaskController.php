<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Form\TaskType;

final class TaskController extends AbstractController{

    public function __construct(private ProjectRepository $projectRepository, private TaskRepository $taskRepository, private EmployeeRepository $employeeRepository) {
    }
    #[Route('/task/new/{id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['project_id' => $id]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $project = $this->projectRepository->find($id);
            $task->setProject($project);
            $manager->persist($task);
            $manager->flush();
            
            return $this->redirectToRoute('app_project_display', ['id' => $id]);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/task/delete/{id}', name: 'app_task_delete', methods: ['GET'])]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {
        $task = $this->taskRepository->find($id);
        $project = $task->getProject();
        
        if($task != null)
        {
            $manager->remove($task);	
            $manager->flush();
        }

        return $this->redirectToRoute('app_project_display', ['id' => $project->getId()]);
    }
    
    #[Route('/task/edit/{id}', name: 'app_task_update', methods: ['GET', 'POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $task = $this->taskRepository->find($id);
        $project = $task->getProject();
        $form = $this->createForm(TaskType::class, $task, [
            'project_id' => $project->getId(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            
            return $this->redirectToRoute('app_project_display', ['id' => $project->getId()]);
        }

        return $this->render('task/update.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }
    
}
