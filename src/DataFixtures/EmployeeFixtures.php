<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Monolog\DateTimeImmutable;


class EmployeeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 4; $i++) {
            $employee = new Employee();
            $employee->setName($faker->lastName);
            $employee->setFirstName($faker->firstName);
            $employee->setEmail($faker->email);
            $date = $faker->dateTimeBetween('-10 years', 'now');
            $dateTimeImmutable = DateTimeImmutable::createFromMutable($date);
            $employee->setDate($dateTimeImmutable);
            $j = rand(1, 3);
            switch ($j) {
                case 1:
                    $employee->setStatus(EmployeeStatus::cdd);
                    break;
                case 2: 
                    $employee->setStatus(EmployeeStatus::cdi);
                    break;
                case 3:
                    $employee->setStatus(EmployeeStatus::freelance);
                    break;
            }
            $manager->persist($employee);
        } 

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
