<?php

namespace App\Exception;

class UserIsNotModeratorException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User is not a moderator of this news');
    }
}