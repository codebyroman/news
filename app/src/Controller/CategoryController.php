<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Model\CategoryDetailsResponse;
use App\Model\CreateCategoryRequest;
use App\Model\ErrorResponse;
use App\Model\ListOfCategoriesResponse;
use App\Model\SuccessResponse;
use App\Service\CategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
    )
    {
    }


    #[Route('/categories', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns a list of categories', attachables: [new Model(type: ListOfCategoriesResponse::class)])]
    public function findListOfCategories(): JsonResponse
    {
        // TODO add filters
        return $this->json($this->categoryService->findAll());
    }

    #[Route('/category/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns category details by ID', attachables: [new Model(type: CategoryDetailsResponse::class)])]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function findCategory(#[MapEntity] Category $category): JsonResponse
    {
        return $this->json($this->categoryService->prepareCategoryDetails($category));
    }

    #[Route('/category', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    #[OA\Response(response: 200, description: 'Creates a category', attachables: [new Model(type: CategoryDetailsResponse::class)])]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateCategoryRequest::class)])]
    public function createCategory(#[MapRequestPayload] CreateCategoryRequest $createCategoryRequest): JsonResponse
    {
        return $this->json($this->categoryService->createFromRequest($createCategoryRequest));
    }

    #[Route('/category/{id}', methods: ['DELETE'])]
    #[IsGranted(User::ROLE_ADMIN)]
    #[OA\Response(response: 200, description: 'Removes the category by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function deleteCategory(#[MapEntity] Category $category): JsonResponse
    {
        $this->categoryService->removeCategory($category);

        return $this->json(new SuccessResponse());
    }

    #[Route('/category/{categoryId}/assign-moderator/{userId}', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    #[OA\Response(response: 200, description: 'Assign the category to the moderator', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 404, description: 'Category or user not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function assignCategoryToModerator(#[MapEntity] Category $category, #[MapEntity] User $user): JsonResponse
    {
        $this->categoryService->assignModerator($category, $user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/category/{categoryId}/deassign-moderator/{moderatorId}', methods: ['POST'])]
    #[IsGranted(User::ROLE_ADMIN)]
    #[OA\Response(response: 200, description: 'Deassign the category to the moderator', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 404, description: 'Category or user not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function deassignCategoryToModerator(#[MapEntity] Category $category, #[MapEntity] User $user): JsonResponse
    {
        $this->categoryService->deassignModerator($category, $user);

        return $this->json(new SuccessResponse());
    }
}
