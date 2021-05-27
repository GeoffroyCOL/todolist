<?php

namespace App\Controller\Back;

use App\Entity\Task;
use App\Service\TaskService;
use App\Form\Task\TaskAddType;
use App\Form\Task\TaskEditType;
use App\Service\ProjectService;
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
class TaskController extends AbstractController
{   
    public function __construct(
        private ProjectService $projectService,
        private TaskService $taskService    
    )
    {}

    /**
     * addTask
     *
     * @return Response
     */
    #[Route('/admin/task/add', name: 'task.add')]
    public function addTask(Request $request): Response
    {
        //Récupère id du projet
        if ($request->query->get('project')) {
            $project_id = $request->query->get('project');
        }

        if (! isset($project_id) || preg_match('#[a-z]#i', $project_id )) {
            $this->addFlash('danger', 'Le projet n\'existe pas.');
            return $this->redirectToRoute('dashboard');
        }

        $project = $this->projectService->getProject((int) $project_id);
        $this->denyAccessUnlessGranted('PROJECT_OWN', $project, 'Vous ne pouvez ajouter de tâche à ce projet');

        $task = new Task();
        $task->setProject($project)
            ->setCreatedBy($this->getUser())
        ;

        $form = $this->createForm(TaskAddType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->persist($task);
            $this->addFlash('success', 'Votre tâche à bien été ajoutée.');
            return $this->redirectToRoute('project.show', ['id' => $project_id]);
        }

        return $this->render('back/task/management.html.twig', [
            'form'      => $form->createView(),
            'action'    => 'ajouter'
        ]);
    }
    
    /**
     * @param  Request $request
     * @param  Task $task
     * @return Response
     */
    #[Route('/admin/task/edit/{id}', name: 'task.edit', requirements: ['id' => '\d+'])]
    public function editTask(Request $request, Task $task): Response
    {
        $this->denyAccessUnlessGranted('TASK_OWN', $task, 'Vous ne pouvez modifier cette tâche.');
        
        $form = $this->createForm(TaskEditType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->persist($task);
            $this->addFlash('success', 'Votre tâche à bien été modifiée.');
            return $this->redirectToRoute('project.show', ['id' => $task->getProject()->getId()]);
        }

        return $this->render('back/task/management.html.twig', [
            'form'      => $form->createView(),
            'action'    => 'modifier'
        ]);
    }
}
