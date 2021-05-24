<?php

namespace Test\controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DashboardControllerTest extends WebTestCase
{
    /**
     * testRouteForDashboardcontrollerNotLogin
     * Si un utilisateur n'est pas connecté alors il ne pourra pas accéder à la page "dashboard"
     *
     * @return void
     */
    public function testRouteForDashboardcontrollerNotLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/admin/dashboard');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * testRouteForDashboardcontrollerLogin
     * Si un utilisateur est connecté alors il pourra accéder à la page "dashboard"
     *
     * @return void
     */
    public function testRouteForDashboardcontrollerLogin(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, '/admin/dashboard');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
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