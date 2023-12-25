<?php

namespace App\Exception;

class WrongStatusException extends \RuntimeException
{
    public function __construct($message = null)
    {
        parent::__construct($message ?? 'Wrong status');
    }
}