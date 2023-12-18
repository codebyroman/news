<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use App\Repository\UserRepository;

class ModeratorResolver
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function resolve(News $news): User
    {
        return $this->userRepository->findLeastBusyModerator($news->getCategories()->getKeys());
    }
}