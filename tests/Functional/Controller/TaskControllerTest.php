<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskControllerTest extends WebTestCase
{

    private KernelBrowser|null $client = null;
    private UrlGeneratorInterface|null $urlGenerator = null;

    private $manager;
    private $testUser;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->manager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $userRepository = $this->manager->getRepository(User::class);

        $this->testUser = $userRepository->findOneByEmail('admin@todo.fr');

        $this->client->loginUser($this->testUser);

        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testListAction()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("To Do List app")')->count());
    }

    public function testCreateAction()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form["task[title]"] = 'Great title';
        $form["task[content]"] = 'Great content';

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertAnySelectorTextContains('div.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    public function testEditAction()
    {

        $taskData = [
            'title' => 'Original Title',
            'content' => 'Original Content',
        ];

        // Create a new task in the database
        $task = new Task();
        $task->setTitle($taskData['title']);
        $task->setContent($taskData['content']);
        $task->setUser($this->testUser);
        $this->manager->persist($task);
        $this->manager->flush();

        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');


        // Submit the edit form with updated data
        $form = $crawler->selectButton('Modifier')->form();
        $this->client->submit($form);

        $form["task[title]"] = 'Updated title';
        $form["task[content]"] = 'Updated content';

        // Follow the redirect and check the content
        $this->client->followRedirect();

        // Check for success flash message
        $this->assertAnySelectorTextContains('div.alert.alert-success', 'La tâche a bien été modifiée.');

    }

    public function testToggleTaskAction()
    {
        $task = $this->manager->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $taskIsDone = $task->getIsDone();

        $this->client->request('POST', '/tasks/' . $task->getId() . '/toggle');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(!$taskIsDone, $task->getIsDone());
    }

    public function testDeleteTaskAction()
    {
        $task = $this->manager->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->client->request('POST', '/tasks/' . $task->getId() . '/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertAnySelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');

        $task = $this->manager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals(null, $task);
    }

    public function testCompletedTaskList() {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_completed_task'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


}
