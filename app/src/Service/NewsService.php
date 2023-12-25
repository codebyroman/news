<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use App\Exception\NoCategoriesWithGivenIdsForNews;
use App\Exception\UserIsNotAuthorException;
use App\Exception\UserIsNotModeratorException;
use App\Exception\WrongStatusException;
use App\Model\CreateNewsRequest;
use App\Model\IdResponse;
use App\Model\ListOfNewsRequest;
use App\Model\ListOfNewsResponse;
use App\Model\NewsDetailsResponse;
use App\Model\NewsStatusesResponse;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;

class NewsService
{
    const PAGE_LIMIT = 10;

    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
        private readonly int $pageLimit,
    )
    {
    }

    public function findByRequest(ListOfNewsRequest $listOfNewsRequest): ListOfNewsResponse
    {
        $items = [];
        $paginator = $this->newsRepository->findByFilter(
            $listOfNewsRequest->filter,
            PaginationUtils::calcOffset($listOfNewsRequest->page, $this->pageLimit),
            $this->pageLimit,
        );

        /** @var News $news */
        foreach ($paginator as $news) {
            $items[] = $this->prepareNewsDetails($news);
        }

        $total = $this->newsRepository->countByFilter($listOfNewsRequest->filter);

        return (new ListOfNewsResponse())
            ->setTotal($total)
            ->setPage($listOfNewsRequest->page)
            ->setPerPage($this->pageLimit)
            ->setPages(PaginationUtils::calcPages($total, $this->pageLimit))
            ->setItems($items);
    }

    public function prepareNewsDetails(News $news): NewsDetailsResponse
    {
        return (new NewsDetailsResponse())
            ->setId($news->getId())
            ->setTitle($news->getTitle())
            ->setContent($news->getContent())
            ->setCategories($news->getCategories())
            ;
    }

    public function createNews(CreateNewsRequest $createNewsRequest, User $user): IdResponse
    {
        $news = (new News())
            ->setTitle($createNewsRequest->getTitle())
            ->setContent($createNewsRequest->getContent())
            ->setAuthor($user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setInactive()
        ;

        $categories = $this->categoryRepository->findByIds($createNewsRequest->getCategoryIds());

        if (!$categories) {
            throw new NoCategoriesWithGivenIdsForNews();
        }

        foreach ($categories as $category) {
            $news->addCategory($category);
        }

        $this->em->persist($news);
        $this->em->flush();

        return new IdResponse($news->getId());
    }

    public function sendForModeration(News $news, User $user): void
    {
        if (!$news->isUserAuthor($user)) {
            throw new UserIsNotAuthorException();
        }

        if (!$news->isInactive() && !$news->isRejected()) {
            throw new WrongStatusException();
        }

        $news->setModerating();

        $this->em->flush();
    }

    public function approve(News $news, User $moderator): void
    {
        $this->changeStatus($news, News::STATUS_APPROVED, $moderator);
    }

    public function reject(News $news, User $moderator): void
    {
        $this->changeStatus($news, News::STATUS_REJECTED, $moderator);
    }

    public function ban(News $news, User $moderator): void
    {
        $this->changeStatus($news, News::STATUS_BANNED, $moderator);
        $this->userService->banUser($news->getAuthor());
    }

    protected function changeStatus(News $news, int $status, User $user): void
    {
        if (!$news->isUserModerator($user)) {
            throw new UserIsNotModeratorException();
        }

        $news->setStatus($status);
        $news->setModeratedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    public function getNewsStatuses(): NewsStatusesResponse
    {
        return new NewsStatusesResponse(News::getStatusLabels());
    }
}