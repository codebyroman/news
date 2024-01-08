<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Model\CreateNewsRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Model\ListOfNewsRequest;
use App\Model\ListOfNewsResponse;
use App\Model\NewsDetailsResponse;
use App\Model\NewsStatusesResponse;
use App\Model\SuccessResponse;
use App\Security\NewsVoter;
use App\Service\NewsService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/api/v1')]
class NewsController extends AbstractController
{
    public function __construct(
        private readonly NewsService $newsService
    )
    {
    }

    #[Route('/news', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns news by request', attachables: [new Model(type: ListOfNewsResponse::class)])]
    public function findListOfNews(#[CurrentUser] ?User $user,
        #[MapQueryString] ListOfNewsRequest $listOfNewsRequest = new ListOfNewsRequest()): JsonResponse
    {
        return $this->json($this->newsService->findByRequest($listOfNewsRequest, $user));
    }

    #[Route('/news/{id}', methods: ['GET'])]
    #[IsGranted(NewsVoter::VIEW, 'news')]
    #[OA\Response(response: 200, description: 'Returns news details by ID', attachables: [new Model(type: NewsDetailsResponse::class)])]
    #[OA\Response(response: 404, description: 'News not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function newsById(#[MapEntity] News $news): JsonResponse
    {
        return $this->json($this->newsService->prepareNewsDetails($news));
    }

    #[Route('/news/statuses', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns news statuses', attachables: [new Model(type: NewsStatusesResponse::class)])]
    public function newsStatuses(): JsonResponse
    {
        return $this->json($this->newsService->getNewsStatuses());
    }

    #[Route('/news', methods: ['POST'])]
    #[IsGranted(User::ROLE_AUTHOR)]
    #[OA\Response(response: 200, description: 'Create news', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 404, description: 'Wrong categories', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateNewsRequest::class)])]
    public function createNews(#[MapRequestPayload] CreateNewsRequest $createNewsRequest,
                               #[CurrentUser] User $user): JsonResponse
    {
        return $this->json($this->newsService->createNews($createNewsRequest, $user));
    }

    #[Route('/news/{id}/send-for-moderation', methods: ['POST'])]
    #[IsGranted(User::ROLE_AUTHOR)]
    #[IsGranted(NewsVoter::EDIT, 'news')]
    #[OA\Response(response: 200, description: 'Send the news for moderation', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function sendForModeration(#[MapEntity] News $news, #[CurrentUser] User $user): JsonResponse
    {
        $this->newsService->sendForModeration($news, $user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/news/{id}/approve', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    #[IsGranted(NewsVoter::EDIT, 'news')]
    #[OA\Response(response: 200, description: 'Approve the news', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function approve(#[MapEntity] News $news, #[CurrentUser] User $user): JsonResponse
    {
        $this->newsService->approve($news, $user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/news/{id}/reject', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    #[IsGranted(NewsVoter::EDIT, 'news')]
    #[OA\Response(response: 200, description: 'Reject the news', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function reject(#[MapEntity] News $news, #[CurrentUser] User $user): JsonResponse
    {
        $this->newsService->reject($news, $user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/news/{id}/ban', methods: ['POST'])]
    #[IsGranted(User::ROLE_MODERATOR)]
    #[IsGranted(NewsVoter::EDIT, 'news')]
    #[OA\Response(response: 200, description: 'Ban the news', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function ban(#[MapEntity] News $news, #[CurrentUser] User $user): JsonResponse
    {
        $this->newsService->ban($news, $user);

        return $this->json(new SuccessResponse());
    }
}
