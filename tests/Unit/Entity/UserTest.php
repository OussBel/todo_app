<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testId(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());
    }

    public function testRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user->setRoles($roles);
        $this->assertEquals($roles, $user->getRoles());
    }

    public function testPassword(): void
    {
        $user = new User();
        $password = 'password123';
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());
    }

    public function testUsername(): void
    {
        $user = new User();
        $username = 'testuser';
        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());
    }

    public function testTaskCollection(): void
    {
        $user = new User();
        $task1 = new Task();
        $task2 = new Task();

        $user->addTask($task1);
        $user->addTask($task2);

        $this->assertCount(2, $user->getTask());

        $user->removeTask($task1);
        $this->assertCount(1, $user->getTask());

        $user->removeTask($task2);
        $this->assertCount(0, $user->getTask());
    }
}
