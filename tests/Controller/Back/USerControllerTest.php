<?php

namespace Tests\Controller\Back;

use App\Repository\UserRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    
    /**
     * testAccessEditProfilNotLogin
     * Si l'utilisateur n'est pas connecté alors il ne peut y accéder
     *
     * @return void
     */
    public function testAccessEditProfilNotLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/admin/user/edit/1');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
    
    /**
     * testAccessEditProfilLogin
     * si l'utilisateur est connecté et possède les informations du compte
     *
     * @return void
     */
    public function testAccessEditProfilLogin(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, '/admin/user/edit/1');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * testAccessEditProfilLoginNotOwn
     * si l'utilisateur est connecté et ne possède pas les informations du compte
     *
     * @return void
     */
    public function testAccessEditProfilLoginNotOwn(): void
    {
        $client = static::createClient();
        $this->login($client);

        $client->request(Request::METHOD_GET, '/admin/user/edit/2');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
    
    /**
     * testEditProfil
     * Test si un utilisateur peut modifier ses données
     *
     * @return void
     */
    public function testEditProfil(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request(Request::METHOD_GET, '/admin/user/edit/1');

        $form = $crawler->selectButton('modifier')->form();

        $form['user_edit[email]'] = 'username@domaine.edit';
        $form['user_edit[newPassword]'] = 'Hum123';
        $client->submit($form);

        //Redirection vers la page de connexion
        $this->assertEquals(true, $client->getResponse()->isRedirect('/admin/dashboard'));
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