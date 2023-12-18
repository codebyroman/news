<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Model\CreateNewsRequest;
use App\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/news')]
class NewsController extends AbstractController
{
    public function __construct(
        private readonly NewsService $newsService
    )
    {
    }

    #[Route('/', methods: ['GET'])]
    public function news(#[MapQueryParameter] int $page): JsonResponse
    {
        return $this->json($this->newsService->getNewsList($page, News::STATUS_ACTIVE));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function newsById(int $id): JsonResponse
    {
        return $this->json($this->newsService->getNewsById($id));
    }

    #[Route('/', methods: ['POST'])]
    #[IsGranted(User::ROLE_AUTHOR)]
    public function createNews(#[MapRequestPayload] CreateNewsRequest $createNewsRequest,
                               #[CurrentUser] UserInterface $user): JsonResponse
    {
        return $this->json($this->newsService->createNews($createNewsRequest, $user));
    }

    #[Route('/{id}/approve', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    public function newsApprove(#[CurrentUser] UserInterface $user): JsonResponse
    {
        // TODO
    }

    #[Route('/{id}/reject', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    public function newsReject(#[CurrentUser] UserInterface $user): JsonResponse
    {
        // TODO
    }

    #[Route('/{id}/ban', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    public function newsBan(#[CurrentUser] UserInterface $user): JsonResponse
    {
        // TODO
    }
}
