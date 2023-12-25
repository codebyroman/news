<?php

namespace App\Exception;

class UserHasNoVerificationCodeException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User has no verification code');
    }
}
