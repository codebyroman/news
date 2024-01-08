<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class RoleService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function grantAdmin(User $user): void
    {
        $this->grantRole($user, User::ROLE_ADMIN);
    }

    public function grantAuthor(User $user): void
    {
        $this->grantRole($user, User::ROLE_AUTHOR);
    }

    public function grantModerator(User $user): void
    {
        $this->grantRole($user, User::ROLE_MODERATOR);
    }

    private function grantRole(User $user, string $role): void
    {
        $user->setRoles([$role]);

        $this->em->persist($user);
        $this->em->flush();
    }
}