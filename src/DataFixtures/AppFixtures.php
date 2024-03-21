<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        //List of users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername($faker->unique()->name)
                ->setEmail($faker->unique()->email)
                ->setPassword($this->passwordHasher->hashPassword($user, 'secret'))
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        //admin
        $adminUser = new User();
        $adminUser->setUsername('admin')
            ->setEmail('admin@todo.fr')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($adminUser);

        //Creation of an anonymous user
        $anonymousUser = new User();
        $anonymousUser->setUsername('anonymous')
            ->setEmail('anonymous@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($anonymousUser, 'secret'))
            ->setRoles(['ROLE_ANONYMOUS']);

        $manager->persist($anonymousUser);

        //Creation of tasks

        for ($i = 0; $i < 20; $i++) {
            $task = new Task();

            $task->setTitle($faker->word)
                ->setContent($faker->paragraph)
                ->setUser($anonymousUser)
                ->setIsDone(false);

            $manager->persist($task);

        }

        $manager->flush();
    }


}
