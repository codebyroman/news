<?php

namespace App\Exception;

class NothingFoundByRequestException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Nothing found by condition');
    }
}
