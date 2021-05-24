<?php

namespace App\Test\Entity\User;

use App\Entity\User;
use App\Tests\Traits\AssertHasErrors;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use AssertHasErrors;

    /**
     * getEntity.
     */
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('Geoffroy')
            ->setPassword('Hum123')
            ->setEmail('geoffroy@gmail.com')
        ;
    }

    /**
     * testUniqueUsername.
     */
    public function testUniqueUsername(): void
    {
        $user = $this->getEntity();
        $user->setUsername('username');
        $this->assertHasErrors($user, 1);
    }

    /**
     * testLengthUsername
     * Le pseudo de l'utilisateur doit contenir au minimum 4 carctères.
     */
    public function testLengthUsername(): void
    {
        $user = $this->getEntity();
        $user->setUsername('use');
        $this->assertHasErrors($user, 1);
    }

    /**
     * testGoodEmail.
     */
    public function testGoodEmail(): void
    {
        $user = $this->getEntity();
        $user->setEmail('usevrpejgvep.fr');
        $this->assertHasErrors($user, 1);
    }

    /**
     * testGoodPassword
     * Le mot de passe doit contenir au minimum 6 caractères dont une majuscule et un chiffre.
     */
    public function testGoodPassword(): void
    {
        $user = $this->getEntity();
        $user->setPassword('usevr');
        $user->setNewpassword('usevr');
        $this->assertHasErrors($user, 2);
    }

    /**
     * testNotBlankUser.
     */
    public function testNotBlankUser(): void
    {
        $this->assertHasErrors(new User(), 3);
    }
}
