<?php

namespace App\Exception;

class WrongVerificationCodeException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Wrong verification code');
    }
}
