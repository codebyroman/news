<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\CreateUserRequest;
use App\Model\IdResponse;
use App\Model\ListOfUsersRequest;
use App\Model\ListOfUsersResponse;
use App\Model\UserDetailsResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly int $pageLimit,
    )
    {
    }

    public function findByRequest(ListOfUsersRequest $findUsersRequest): ListOfUsersResponse
    {
        $items = [];
        $paginator = $this->userRepository->findByFilter(
            $findUsersRequest->filter,
            PaginationUtils::calcOffset($findUsersRequest->page, $this->pageLimit),
            $this->pageLimit,
        );

        /** @var User $user */
        foreach ($paginator as $user) {
            $items[] = $this->prepareUserDetails($user);
        }

        $total = $this->userRepository->countByFilter($findUsersRequest->filter);

        return (new ListOfUsersResponse())
            ->setTotal($total)
            ->setPage($findUsersRequest->page)
            ->setPerPage($this->pageLimit)
            ->setPages(PaginationUtils::calcPages($total, $this->pageLimit))
            ->setItems($items);
    }

    public function prepareUserDetails(User $user): UserDetailsResponse
    {
        return (new UserDetailsResponse())
            ->setId($user->getId())
            ->setEmail($user->getEmail())
            ->setStatus($user->getStatus());

    }

    public function createFromRequest(CreateUserRequest $signUpRequest): IdResponse
    {
        if ($this->userRepository->existsByEmail($signUpRequest->email)) {
            throw new UserAlreadyExistsException();
        }

        $user = (new User())
            ->setName($signUpRequest->name)
            ->setEmail($signUpRequest->email)
            ->setStatus(User::STATUS_ACTIVE);

        $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->password));

        $this->em->persist($user);
        $this->em->flush();

        return new IdResponse($user->getId());
    }

    public function activateUser(User $user): void
    {
        $this->setStatusToUser($user, User::STATUS_ACTIVE);
    }

    public function inactivateUser(User $user): void
    {
        $this->setStatusToUser($user, User::STATUS_INACTIVE);
    }

    public function banUser(User $user): void
    {
        $this->setStatusToUser($user, User::STATUS_BANNED);
    }

    protected function setStatusToUser(User $user, int $status): void
    {
        $user->setStatus($status);

        $this->em->persist($user);
        $this->em->flush();
    }

}