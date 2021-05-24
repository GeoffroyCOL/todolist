<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserRepository $repository,
        private UserPasswordEncoderInterface $encoder
    ) {
    }
    
    /**
     * @param  User $user
     * @return void
     */
    public function persist(User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->manager->persist($user);
        $this->manager->flush();
    }
    
    /**
     * @param  User $user
     * @return void
     */
    public function update(User $user): void
    {
        if ($user->getNewpassword()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getNewpassword()));
        }

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
