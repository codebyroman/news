<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Exception\CategoryAlreadyExistsException;
use App\Exception\UserIsNotModeratorException;
use App\Model\CategoryDetailsResponse;
use App\Model\CreateCategoryRequest;
use App\Model\IdResponse;
use App\Model\ListOfCategoriesResponse;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em
    )
    {
    }

    public function findAll(): ListOfCategoriesResponse
    {
        $categories = $this->categoryRepository->findAll();
        $response = new ListOfCategoriesResponse();

        foreach ($categories as $category) {
            $response->addCategory($this->prepareCategoryDetails($category));
        }

        return $response;
    }

    public function prepareCategoryDetails(Category $category): CategoryDetailsResponse
    {
        return (new CategoryDetailsResponse())
            ->setId($category->getId())
            ->setName($category->getName());
    }


    public function createFromRequest(CreateCategoryRequest $createCategoryRequest): IdResponse
    {
        if ($this->categoryRepository->existsByName($createCategoryRequest->getName())) {
            throw new CategoryAlreadyExistsException();
        }

        $category = (new Category())->setName($createCategoryRequest->getName());

        $this->em->persist($category);
        $this->em->flush();

        return (new IdResponse($category->getId()));
    }

    public function removeCategory(Category $category): void
    {
        $this->em->remove($category);
        $this->em->flush();
    }

    public function assignModerator(Category $category, User $user): void
    {
        if (!$user->isModerator()) {
            throw new UserIsNotModeratorException();
        }

        $category->addModerator($user);
        $this->em->flush();
    }

    public function deassignModerator(Category $category, User $user): void
    {
        $category->removeModerator($user);
        $this->em->flush();
    }
}