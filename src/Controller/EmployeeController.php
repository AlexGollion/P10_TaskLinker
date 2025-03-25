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
use App\Entity\Employee;
use App\Form\EmployeeType;

final class EmployeeController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository, private TaskRepository $taskRepository, private EmployeeRepository $employeeRepository) {
    }
    #[Route('/employees', name: 'app_employees_display')]
    public function employees(): Response
    {
        $employees = $this->employeeRepository->findAll();

        return $this->render('employee/employees.html.twig', [
            'employees' =>$employees,
        ]);
    }

    #[Route('/employees/delete/{id}', name: 'app_employees_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
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

    #[Route('/employees/edit/{id}', name: 'app_employees_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $employee = $this->employeeRepository->find($id);
        $status = $employee->getStatus() ?? EmployeeStatus::cdi;
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            
            return $this->redirectToRoute('app_employees_display');
        }

        return $this->render('employee/update.html.twig', [
            'employee' =>$employee,
            'form' => $form,
        ]);
    }
}
