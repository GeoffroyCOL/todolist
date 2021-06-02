<?php

namespace App\Controller\Back;

use App\Service\ProjectService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * DashboardController
 * @Security("is_granted('ROLE_ADMIN_LIST')", statusCode=403, message="Vous ne pouvez pas accéder à cette zone")
 */
class DashboardController extends AbstractController
{    
    public function __construct(
        private ProjectService $projectService
    )
    {}

    /**
     * @return Response
     */
    #[Route('/admin/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('back/dashboard/index.html.twig', [
            'controller_name'   => 'DashboardController',
            'current_page'      => 'dashboard',
            'component'         => 'admin',
            'projects'          => $projects = $this->projectService->getProjectsByUser($user)
        ]);
    }
}
