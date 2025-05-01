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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\TaskVoter;
use App\Entity\Task;
use App\Form\TaskType;

#[Route('/task', name: 'app_task_')]
#[IsGranted('ROLE_USER')]
final class TaskController extends AbstractController{

    public function __construct(private ProjectRepository $projectRepository, private TaskRepository $taskRepository, private EmployeeRepository $employeeRepository) {
    }
    #[Route('/new/{id}', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
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
    
    #[Route('/edit/{id}', name: 'update', methods: ['GET', 'POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $task = $this->taskRepository->find($id);
        $project = $task->getProject();

        $this->denyAccessUnlessGranted(TaskVoter::VIEW, $task);

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
