<?php

namespace App\Exception;

class NoNeedToVerifyUserException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No need to verify this user');
    }
}
