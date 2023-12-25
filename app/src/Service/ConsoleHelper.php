<?php

namespace App\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConsoleHelper
{
    public function writeValidationErrors(ConstraintViolationListInterface $errors, OutputInterface $output)
    {
        $output->writeln('<error>Validation error</error>');
        foreach ($errors as $error) {
            $output->writeln("<error>{$error->getPropertyPath()}: {$error->getMessage()}</error>");
        }
    }
}