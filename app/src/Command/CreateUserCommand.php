<?php

namespace App\Command;

use App\Model\CreateUserRequest;
use App\Service\ConsoleHelper;
use App\Service\CreateUserOperation;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    aliases: ['app:add-user'],
    hidden: false
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ValidatorInterface $validator,
        private readonly ConsoleHelper $consoleHelper
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');

        $questionPassword = new Question('User password');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $password = $questionHelper->ask($input, $output, $questionPassword);

        $questionConfirmPassword = new Question('Confirm user password');
        $questionConfirmPassword->setHidden(true);
        $questionConfirmPassword->setHiddenFallback(false);

        $confirmPassword = $questionHelper->ask($input, $output, $questionConfirmPassword);

        $createUserRequest = (new CreateUserRequest(
            $input->getArgument('name'),
            $input->getArgument('email'),
            $password,
            $confirmPassword
        ));

        $errors = $this->validator->validate($createUserRequest);

        if (count($errors)) {
            $this->consoleHelper->writeValidationErrors($errors, $output);

            return Command::FAILURE;
        }

        $this->userService->createFromRequest($createUserRequest);

        return Command::SUCCESS;
    }
}