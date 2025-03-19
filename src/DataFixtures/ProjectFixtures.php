<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 3; $i++) {
            $project = new Project();
            $project->setTitle($faker->company);
            $project->setArchive(false); 
            $manager->persist($project);

        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
