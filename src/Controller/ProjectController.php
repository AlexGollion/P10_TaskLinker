<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\EmployeeRepository;
use App\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\ProjectVoter;

#[Route('/project', name: 'app_project_')]
#[IsGranted('ROLE_USER')]
final class ProjectController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository, private TaskRepository $taskRepository, private EmployeeRepository $employeeRepository) {
    }
    #[Route('/{id}', name: 'display', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function project(int $id): Response
    {
        $project = $this->projectRepository->find($id);

        $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $project);
    
        $tasks = $this->taskRepository->findBy(['project' => $project]);

        return $this->render('project/project.html.twig', [
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            //On ajoute les nouveaux employés au projet
            foreach($formData->getEmployees() as $employee)
            {
                $employeeProjects = $employee->getProjects();
                if(!($employeeProjects->contains($project)))
                {
                    $employee->addProject($project);
                }
            }
            $project->setArchive(false);
            $manager->persist($project);
            $manager->flush();
            
            return $this->redirectToRoute('app_project_display', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {

        $project = $this->projectRepository->find($id);
        $tasks = $this->taskRepository->findBy(['project' => $project]);

        if($project != null)
        {
            foreach($tasks as $task)
            {
                $manager->remove($task);
            }

            foreach($project->getEmployees() as $employee)
            {
                $employee->removeProject($project);
            }

            $manager->remove($project);
            $manager->flush();
        }

        return $this->redirectToRoute('app_main_home');
    }

    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, int $id, EntityManagerInterface $manager): Response
    {

        $project = $this->projectRepository->find($id);
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $newEmployees = $formData->getEmployees();

            $allEmployees = $this->employeeRepository->findAll();
            //On supprime les employés qui ne sont plus dans le formulaire
            foreach($allEmployees as $employee)
            {
                if(!($newEmployees->contains($employee)) && $employee->getProjects()->contains($project))
                {
                    $employee->removeProject($project);
                }
            }

            //On ajoute les nouveaux employés au projet
            foreach($newEmployees as $employee)
            {
                $employeeProjects = $employee->getProjects();
                if(!($employeeProjects->contains($project)))
                {
                    $employee->addProject($project);
                }
            }
            $manager->flush();
            
            return $this->redirectToRoute('app_project_display', ['id' => $project->getId()]);
        }

        return $this->render('project/update.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

}
