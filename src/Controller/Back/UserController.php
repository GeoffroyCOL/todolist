<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\User\UserEditType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{   
    public function __construct(private UserService $userService)
    {}

    /**
     * @param  User $user
     * @return Response
     */
    #[Route('/admin/user/edit/{id}', name: 'user.edit', requirements: ['id' => '\d+'])]
    public function editUser(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('USER_OWN', $user, 'Vous ne pouvez pas modifier ce profil');

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->update($user);
            $this->addFlash('success', 'Votre profil à bien été modifié.');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('back/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param  User $user
     * @return void
     */
    #[Route('/admin/user/delete/{id}', name: 'user.delete', requirements: ['id' => '\d+'])]
    public function deleteUser(User $user)
    {
        $this->denyAccessUnlessGranted('USER_OWN', $user, 'Vous ne pouvez pas supprimer ce profil');

        $this->userService->delete($user);
        return $this->redirectToRoute('home');
    }
}
