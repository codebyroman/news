<?php

namespace App\Exception;

class NoModeratorForCategoryException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No moderator for this category');
    }
}
