<?php

namespace App\Exception;

class UserIsNotAuthorException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User is not an author of this news');
    }
}