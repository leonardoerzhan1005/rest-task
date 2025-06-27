<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testGetTasks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }
} 