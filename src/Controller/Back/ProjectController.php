<?php

namespace App\Controller\Back;

use App\Entity\Project;
use App\Form\Project\ProjectAddType;
use App\Service\ProjectService;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * ProjectController
 * @Security("is_granted('ROLE_ADMIN_LIST')", statusCode=403, message="Vous ne pouvez pas accéder à cette zone")
 */
class ProjectController extends AbstractController
{
    use ReloadDatabaseTrait;

    public function __construct(private ProjectService $projectService)
    {}

    #[Route('/admin/projects', name: 'projects.list')]
    public function listProject(): Response
    {
        return $this->render('back/project/list.html.twig');
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
            'form' => $form->createView()
        ]);
    }
}
