<?php

namespace App\Service;

use App\Model\CategoriesListResponse;
use App\Model\CreateNewsRequest;
use App\Model\IdResponse;
use App\Model\NewsDetailsResponse;
use App\Entity\News;
use App\Exception\NoCategoriesWithGivenIdsForNews;
use App\Model\NewsListResponse;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsService
{
    const PAGE_LIMIT = 10;

    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function createNews(CreateNewsRequest $createNewsRequest, UserInterface $user): IdResponse
    {
        $news = (new News())
            ->setTitle($createNewsRequest->getTitle())
            ->setContent($createNewsRequest->getContent())
            ->setAuthor($user);

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

    public function getNewsList(int $status, int $page): NewsListResponse
    {
        $items = [];
        $paginator = $this->newsRepository->getNewsByStatusPaginated(
            $status,
            PaginationUtils::calcOffset($page, self::PAGE_LIMIT),
            self::PAGE_LIMIT
        );

        foreach ($paginator as $news) {
            $items[] = NewsDetailsResponse::createByEntity($news);
        }

        $total = $this->newsRepository->countByStatus(News::STATUS_ACTIVE);

        return (new NewsListResponse())
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(PaginationUtils::calcPages($total, self::PAGE_LIMIT))
            ->setItems($items);
    }

    public function getNewsById(int $id): NewsDetailsResponse
    {
        $news = $this->newsRepository->find($id);

        return NewsDetailsResponse::createByEntity($news);
    }
}