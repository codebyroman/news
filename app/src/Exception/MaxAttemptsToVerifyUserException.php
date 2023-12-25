<?php

namespace App\Exception;

class MaxAttemptsToVerifyUserException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Max attempts to verify the user have been reached');
    }
}
