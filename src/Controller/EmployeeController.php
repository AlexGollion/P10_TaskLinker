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
use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Enum\EmployeeStatus;

#[Route('/employees', name: 'app_employees_')]
#[IsGranted('ROLE_ADMIN')]
final class EmployeeController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository, private TaskRepository $taskRepository, private EmployeeRepository $employeeRepository) {
    }

    #[Route('/', name: 'display')]
    public function employees(): Response
    {
        $employees = $this->employeeRepository->findAll();

        return $this->render('employee/employees.html.twig', [
            'employees' =>$employees,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id, EntityManagerInterface $manager): Response
    {

        $employee = $this->employeeRepository->find($id);
        $tasks = $this->taskRepository->findBy(['employee' => $employee]);

        if($employee != null)
        {
            foreach($tasks as $task)
            {
                $manager->remove($task);
            }

            foreach($employee->getProjects() as $project)
            {
                $project->removeEmployee($employee);
            }

            $manager->remove($employee);
            $manager->flush();
        }

        return $this->redirectToRoute('app_employees_display');
    }

    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $employee = $this->employeeRepository->find($id);      
        $status = $employee->getStatus();  
        $form = $this->createForm(EmployeeType::class, $employee, [
            'status' => $status,
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $status = $form->get('status')->getData();
            $employee->setStatus($status);

            $roles = [];
            $role = $form->get('roles')->getData();
            if ($role == 'ROLE_ADMIN')
            {
                $roles[] = 'ROLE_USER';
                $roles[] = 'ROLE_ADMIN';
                $employee->setRoles($roles);
            }
            else 
            {
                $roles[] = 'ROLE_USER';
                $employee->setRoles($roles);
            }

            $manager->flush();
            
            return $this->redirectToRoute('app_employees_display');
        }

        return $this->render('employee/update.html.twig', [
            'employee' =>$employee,
            'form' => $form,
        ]);
    }
}
