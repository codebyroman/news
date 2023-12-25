<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\CreateUserRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Model\ListOfUsersRequest;
use App\Model\ListOfUsersResponse;
use App\Model\SuccessResponse;
use App\Model\UserDetailsResponse;
use App\Service\RoleService;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
#[IsGranted(User::ROLE_ADMIN)]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RoleService $roleService,
    )
    {
    }

    #[Route('/users', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns users', attachables: [new Model(type: ListOfUsersResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: ListOfUsersRequest::class)])]
    public function findListOfUsers(#[MapQueryString] ListOfUsersRequest $findUsersRequest): Response
    {
        return $this->json($this->userService->findByRequest($findUsersRequest));
    }

    #[Route('/user/{id}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns user details by ID', attachables: [new Model(type: UserDetailsResponse::class)])]
    #[OA\Response(response: 404, description: 'User not found', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function findUser(#[MapEntity] User $user): Response
    {
        return $this->json($this->userService->prepareUserDetails($user));
    }

    #[Route('/user', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Creates a user', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function createUser(#[MapRequestPayload] CreateUserRequest $createUserRequest): Response
    {
        return $this->json($this->userService->createFromRequest($createUserRequest));
    }

    #[Route('/user/{id}/grant-author', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Grant author role to the user by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function grantAuthor(#[MapEntity] User $user): Response
    {
        $this->roleService->grantAuthor($user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/user/{id}/grant-moderator', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Grant author role to the user by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function grantModerator(#[MapEntity] User $user): Response
    {
        $this->roleService->grantModerator($user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/user/{id}/ban', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Grant author role to the user by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function banUser(#[MapEntity] User $user): Response
    {
        $this->userService->banUser($user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/user/{id}/activate', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Grant author role to the user by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function activateUser(#[MapEntity] User $user): Response
    {
        $this->userService->activateUser($user);

        return $this->json(new SuccessResponse());
    }

    #[Route('/user/{id}/inactivate', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Grant author role to the user by ID', attachables: [new Model(type: SuccessResponse::class)])]
    #[OA\Response(response: 403, description: 'Forbidden', attachables: [new Model(type: ErrorResponse::class)])]
    public function inactivateUser(#[MapEntity] User $user): Response
    {
        $this->userService->inactivateUser($user);

        return $this->json(new SuccessResponse());
    }
}
