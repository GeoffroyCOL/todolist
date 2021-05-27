<?php

namespace App\Controller\Back;

use App\Entity\Project;
use App\Service\TaskService;
use App\Service\ProjectService;
use App\Form\Project\ProjectAddType;
use App\Form\Project\ProjectEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * ProjectController
 * @Security("is_granted('ROLE_ADMIN_LIST')", statusCode=403, message="Vous ne pouvez pas accéder à cette zone")
 */
class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectService $projectService,
        private TaskService $taskService
    )
    {}

    /**
     * @return Response
     */
    #[Route('/admin/projects', name: 'projects.list')] 
    public function listProject(): Response
    {
        $user = $this->getUser();
        $projects = $this->projectService->getProjectsByUser($user);

        return $this->render('back/project/list.html.twig', [
            'projects' => $projects
        ]);
    }
    
    /**
     * @param  Project $project
     * @return Response
     */
    #[Route('/admin/project/{id}', name: 'project.show', requirements: ['id' => '\d+'])]
    public function showProject(Project $project): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_OWN', $project, 'Vous ne pouvez pas consulter ce projet');
        
        return $this->render('back/project/show.html.twig', [
            'project'   => $project,
            'tasks'     => $this->taskService->getTasksByProject($project->getId())
        ]);
    }
    
    /**
     * @param  Request $request
     * @return Response
     */
    #[Route('/admin/project/add', name: 'project.add')]
    public function addProject(Request $request): Response
    {
        $project = new Project;
        $form = $this->createForm(ProjectAddType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->persist($project);
            $this->addFlash('success', 'Votre projet à bien été ajouté.');
            return $this->redirectToRoute('projects.list');
        }

        return $this->render('back/project/management.html.twig', [
            'form'      => $form->createView(),
            'action'    => 'ajouter'
        ]);
    }
    
    /**
     * editProject
     *
     * @param  Request $request
     * @param  Project $project
     * @return Response
     */
    #[Route('/admin/project/edit/{id}', name: 'project.edit', requirements: ['id' => '\d+'])]
    public function editProject(Request $request, Project $project): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_OWN', $project, 'Vous ne pouvez pas modifier ce projet');

        $form = $this->createForm(ProjectEditType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->persist($project);
            $this->addFlash('success', 'Votre projet à bien été modifié.');
            return $this->redirectToRoute('projects.list');
        }

        return $this->render('back/project/management.html.twig', [
            'form'      => $form->createView(),
            'action'    => 'modifier'
        ]);
    }
    
    /**
     * @param  Project $project
     * @return Response
     */
    #[Route('/admin/project/delete/{id}', name: 'project.delete', requirements: ['id' => '\d+'])]
    public function deleteProject(Project $project): Response
    {
        $this->denyAccessUnlessGranted('PROJECT_OWN', $project, 'Vous ne pouvez pas supprimer ce projet');
        $this->projectService->delete($project);
        $this->addFlash('success', 'Votre projet à bien été supprimé.');

        return $this->redirectToRoute('projects.list');
    }
}
