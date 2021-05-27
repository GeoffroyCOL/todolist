<?php

namespace Tests\Controller\Back;

use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;

    /**
     * testAccessRouteForTaskControllerWithUserNotConnected
     * Test l'accès aux routes du TaskController ( Back ) si un utilisateur n'est pas connecté
     * 
     * @dataProvider setRouteForTaskControllerNotUserConnected
     *
     * @param  string $route
     * @param  int $response
     * @return void
     */
    public function testAccessRouteForTaskControllerWithUserNotConnected(string $route, int $response): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $route);
        $this->assertEquals($response, $client->getResponse()->getStatusCode());
    }

    /**
     * setRouteForTaskControllerNotUserConnected
     * Données avec la route et la réponse attendu lorsqu'un utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function setRouteForTaskControllerNotUserConnected()
    {
        return [
            "Pour l'ajout d'une nouvelle tâche"  => ['/admin/task/add', Response::HTTP_FOUND],
            "Pour la modification d'une tâche"   => ['/admin/task/edit/11', Response::HTTP_FOUND],
        ];
    }

    /**
     * testAccessRouteForTaskControllerWithUserConnected
     * Test l'accès aux routes du TaskController ( Back ) si un utilisateur n'est pas connecté
     * 
     * @dataProvider setRouteForTaskControllerUserConnected
     *
     * @param  string $route
     * @param  int $response
     * @return void
     */
    public function testAccessRouteForTaskControllerWithUserConnected(string $route, int $response): void
    {
        $client = static::createClient();
        $this->login($client);
        $client->request(Request::METHOD_GET, $route);
        $this->assertEquals($response, $client->getResponse()->getStatusCode());
    }

    /**
     * setRouteForTaskControllerUserConnected
     * Données avec la route et la réponse attendu lorsqu'un utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function setRouteForTaskControllerUserConnected()
    {
        return [
            "Pour l'ajout d'une nouvelle tâche d'un projet m'appartenant"           => ['/admin/task/add?project=1', Response::HTTP_OK],
            "Pour l'ajout d'une nouvelle tâche d'un projet ne m'appartenant pas"    => ['/admin/task/add?project=2', Response::HTTP_FORBIDDEN],
            "Pour l'ajout d'une nouvelle tâche d'un projet qui n'existe pas"        => ['/admin/task/add?project=2000', Response::HTTP_FORBIDDEN],
            "Pour la modification d'une tâche m'appartenant"                        => ['/admin/task/edit/11', Response::HTTP_OK],
            "Pour la modification d'une tâche ne m'appartenant pas"                 => ['/admin/task/edit/12', Response::HTTP_FORBIDDEN]
        ];
    }
    
    /**
     * @return void
     */
    public function testAddTask(): void
    {
        $client = static::createClient();
        $this->login($client);

        //Mauvaises données
        $client->request(Request::METHOD_GET, '/admin/task/add?project=aze');
        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/dashboard'));

        //project n'existe pas
        $client->request(Request::METHOD_GET, '/admin/task/add');
        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/dashboard'));

        $crawler = $client->request(Request::METHOD_GET, '/admin/task/add?project=1');
        $form = $crawler->selectButton('ajouter')->form();

        $form['task_add[name]'] = 'Une tâche ajoutée au projet';
        $form['task_add[description]'] = 'La première tâche ajoutée';
        $client->submit($form);

        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/project/1'));
    }

    public function testEditTask()
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request(Request::METHOD_GET, '/admin/task/edit/11');
        $form = $crawler->selectButton('modifier')->form();

        $form['task_edit[name]'] = 'Une tâche modifiée au projet';
        $form['task_edit[description]'] = 'La première tâche modifiée';
        $form['task_edit[note]'] = 'Une note';
        $client->submit($form);

        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/project/1'));
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