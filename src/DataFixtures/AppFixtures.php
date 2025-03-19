<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Project;
use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $employees = $manager->getRepository(Employee::class)->findAll();
        $projects = $manager->getRepository(Project::class)->findAll();
        $tasks = $manager->getRepository(Task::class)->findAll();

        foreach ($tasks as $task) {
            $employee = $employees[array_rand($employees)];
            $project = $projects[array_rand($projects)];
            $task->setEmployee($employee);
            $task->setProject($project);
            $manager->persist($task);

            $employee->addProject($project);
            $manager->persist($employee);

            $project->addEmployee($employee);
            $project->addTask($task);
            $manager->persist($project);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group2'];
    }
}
