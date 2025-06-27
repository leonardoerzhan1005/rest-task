<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = ['new', 'in_progress', 'done'];
        for ($i = 1; $i <= 10; $i++) {
            $task = new Task();
            $task->setTitle("Task $i");
            $task->setDescription("Description for task $i");
            $task->setStatus($statuses[array_rand($statuses)]);
            $manager->persist($task);
        }
        $manager->flush();
    }
}