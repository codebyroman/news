<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use App\Exception\NoModeratorForCategoryException;
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
        $moderator = $this->userRepository->findLeastBusyModerator($news);

        if (!$moderator) {
            throw new NoModeratorForCategoryException();
        }

        return $moderator;
    }
}