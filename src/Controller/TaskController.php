<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }


    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $loggedInUser = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($loggedInUser);
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    #[IsGranted(TaskVoter::EDIT, subject: 'task')]
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    #[IsGranted(TaskVoter::EDIT, subject: 'task')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $task->toggle(!$task->getIsDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted(TaskVoter::EDIT, subject: 'task')]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
