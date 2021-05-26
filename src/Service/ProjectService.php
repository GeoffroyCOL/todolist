<?php

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProjectService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ProjectRepository $repository,
        private Security $security
    ) {}
    
    /**
     * persist
     *
     * @param  Project $project
     * @return void
     */
    public function persist(Project $project): void
    {
        if (!$project->getId()) {
            $project->setCreatedBy($this->security->getUser());
        }
        $this->manager->persist($project);
        $this->manager->flush();
    }
}