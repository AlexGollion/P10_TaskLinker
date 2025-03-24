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
}
