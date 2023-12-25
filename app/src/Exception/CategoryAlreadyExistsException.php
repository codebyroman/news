<?php

namespace App\Exception;

class CategoryAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Category already exists');
    }
}
