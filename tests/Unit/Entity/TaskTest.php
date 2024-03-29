<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $task = new Task();

        $this->assertNull($task->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $task->getCreatedAt());
        $this->assertNull($task->getTitle());
        $this->assertNull($task->getContent());
        $this->assertFalse($task->getIsDone());
        $this->assertNull($task->getUser());
    }

    public function testSettersAndGetters(): void
    {
        $task = new Task();

        $title = 'Test Title';
        $content = 'Test Content';

        $task->setTitle($title);
        $task->setContent($content);
        $task->setIsDone(true);

        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($content, $task->getContent());
        $this->assertTrue($task->getIsDone());
    }

    public function testToggleMethod(): void
    {
        $task = new Task();

        $task->toggle(false);
        $this->assertFalse($task->getIsDone());
        $task->toggle(true);
        $this->assertTrue($task->getIsDone());
    }

    public function testUserAssociation(): void
    {
        $task = new Task();

        $user = new User();
        $task->setUser($user);

        $this->assertInstanceOf(User::class, $task->getUser());
    }
}
