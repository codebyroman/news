<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/category')]
class CategoryController extends AbstractController
{
    #[Route('/', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        // TODO
    }

    #[Route('/{id}/news', methods: ['GET'])]
    public function getNewsByCategory(int $id): JsonResponse
    {
        // TODO
    }

    #[Route('/', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function createCategory(): JsonResponse
    {
        // TODO
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function deleteCategory(): JsonResponse
    {
        // TODO
    }


    #[Route('/{id}/moderator', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    public function attachCategoryToModerator(): JsonResponse
    {
        // TODO
    }
}
