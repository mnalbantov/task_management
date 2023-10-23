<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Utils\Helper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i < 4; ++$i) {
            $project = new Project();
            $project->setTitle('Project #' . $i);
            $project->setDescription('Loren lipsum lorem' . $i);
            $project->setStatus(Helper::PROJECT_NEW);
            $project->setStartDate(new \DateTime());
            $project->setUserType($faker->randomElement(Helper::$userTypes));

            $manager->persist($project);
        }
        $manager->flush();
    }
}
