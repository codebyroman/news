<?php

namespace App\Command;

use App\Model\IdRequest;
use App\Repository\UserRepository;
use App\Service\ConsoleHelper;
use App\Service\RoleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:grant-admin',
    description: 'Grants admin role to a user.',
    aliases: ['app:grant-admin'],
    hidden: false
)]
class GrantUserAdminCommand extends Command
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly ValidatorInterface $validator,
        private readonly UserRepository $userRepository,
        private readonly ConsoleHelper $consoleHelper
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'User ID')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $idRequest = new IdRequest((int) $input->getArgument('id'));

        $errors = $this->validator->validate($idRequest);

        if (count($errors)) {
            $this->consoleHelper->writeValidationErrors($errors, $output);

            return Command::FAILURE;
        }

        $user = $this->userRepository->find($idRequest->getId());

        if (!$user) {
            throw new UserNotFoundException();
        }

        $this->roleService->grantAdmin($user);

        return Command::SUCCESS;
    }
}