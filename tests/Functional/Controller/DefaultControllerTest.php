<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexAction(): void
    {
        $client = static::createClient();

        // Send a GET request to the homepage
        $client->request('GET', '/');

        // Assert that the response is successful (status code 200)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that the response contains the expected content
        $this->assertStringContainsString('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $client->getResponse()->getContent());

        $this->assertStringContainsString('Créer une nouvelle tâche', $client->getResponse()->getContent());
        $this->assertStringContainsString('Consulter la liste des tâches à faire', $client->getResponse()->getContent());
        $this->assertStringContainsString('Consulter la liste des tâches terminées', $client->getResponse()->getContent());
    }

}
