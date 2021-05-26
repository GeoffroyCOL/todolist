<?php

namespace Tests\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectControllerTest extends WebTestCase
{    
    /**
     * testAccessRouteForProjectControllerWithUserNotConnected
     * Test l'accès aux routes du ProjectController ( Back ) si un utilisateur n'est pas connecté
     * 
     * @dataProvider setRouteForProjectControllerNotUserConnected
     *
     * @param  string $route
     * @param  int $response
     * @return void
     */
    public function testAccessRouteForProjectControllerWithUserNotConnected(string $route, int $response): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $route);
        $this->assertEquals($response, $client->getResponse()->getStatusCode());
    }

    /**
     * testAccessRouteForProjectControllerWithUserConnected
     * Test l'accès aux routes du ProjectController ( Back ) si un utilisateur est connecté
     * 
     * @dataProvider setRouteForProjectControllerUserConnected
     *
     * @param  string $route
     * @param  int $response
     * @return void
     */
    public function testAccessRouteForProjectControllerWithUserConnected(string $route, int $response): void
    {
        $client = static::createClient();
        $this->login($client);
        $client->request(Request::METHOD_GET, $route);
        $this->assertEquals($response, $client->getResponse()->getStatusCode());
    }
    
    /**
     * @return void
     */
    public function testAddProject(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request(Request::METHOD_GET, '/admin/project/add');

        $form = $crawler->selectButton('ajouter')->form();
        $form['project_add[name]'] = 'Projet 1';
        $form['project_add[description]'] = 'Le premier projet à réaliser';
        $client->submit($form);

        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/projects'));
    }
    
    /**
     * @return void
     */
    public function testEditProject(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request(Request::METHOD_GET, '/admin/project/edit/11');

        $form = $crawler->selectButton('modifier')->form();
        $form['project_edit[name]'] = 'Projet modifié';
        $form['project_edit[description]'] = 'Le premier projet à réaliser modifier';
        $form['project_edit[status]'] = 'en cours';
        $client->submit($form);

        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/projects'));
    }

    /**
     * setRouteForProjectControllerNotUserConnected
     * Données avec la route et la réponse attendu lorsqu'un utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function setRouteForProjectControllerNotUserConnected()
    {
        return [
            "Pour l'ajout d'un nouveau projet"          => ['/admin/project/add', Response::HTTP_FOUND],
            "Pour la modification d'un nouveau projet"  => ['/admin/project/edit/11', Response::HTTP_FOUND]
        ];
    }

    /**
     * setRouteForProjectControllerUserConnected
     * Données avec la route et la réponse attendu lorsqu'un utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function setRouteForProjectControllerUserConnected()
    {
        return [
            "Pour l'ajout d'un nouveau projet"          
                => ['/admin/project/add', Response::HTTP_OK],
            "Pour la modification d'un nouveau projet qui m'appartient"  
                => ['/admin/project/edit/11', Response::HTTP_OK],
            "Pour la modification d'un nouveau projet qui ne m'appartient pas"  
                => ['/admin/project/edit/12', Response::HTTP_FORBIDDEN]
        ];
    }

    /**
     * getLogin
     * Simule la connexion d'un utilisateur
     *
     * @param  KernelBrowser $client
     * @return void
     */
    private function login(KernelBrowser $client): void
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('username');
        $client->loginUser($testUser);
    }
}