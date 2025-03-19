<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Monolog\DateTimeImmutable;


class TaskFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 9; $i++) {
            $task = new Task();
            $task->setTitle($faker->company);
            $task->setDescription($faker->text);
            $task->setDate(new \DateTimeImmutable());
            $j = rand(1, 3);
            switch ($j) {
                case 1:
                    $task->setStatus(TaskStatus::ToDo);
                    break;
                case 2: 
                    $task->setStatus(TaskStatus::Doing);
                    break;
                case 3:
                    $task->setStatus(TaskStatus::Done);
                    break;
            }
            $manager->persist($task);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
