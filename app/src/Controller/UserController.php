<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/user')]
class UserController extends AbstractController
{
    #[Route('/{id}/grant-author', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function grantAuthor(): Response
    {
        // TODO
    }

    #[Route('/{id}/grant-moderator', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function grantModerator(): Response
    {
        // TODO
    }

    #[Route('/{id}/ban', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function banUser(): Response
    {
        // TODO
    }

    #[Route('/{id}/activate', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function activateUser(): Response
    {
        // TODO
    }

    #[Route('/{id}/inactivate', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function inactivateUser(): Response
    {
        // TODO
    }
}
