<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private TaskRepository $repository
    ){}
    
    /**
     * @param  int $id
     * @return array
     */
    public function getTasksByProject(int $id): array
    {
        return $this->repository->findBy(['project' => $id]);
    }
    /**
     * @param  Task $task
     * @return void
     */
    public function persist(Task $task): void
    {
        $this->manager->persist($task);
        $this->manager->flush();
    }
    
    /**
     * @param  Task $task
     * @return void
     */
    public function delete(Task $task): void
    {
        $this->manager->remove($task);
        $this->manager->flush();
    }
}