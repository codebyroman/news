<?php

namespace App\Model;

class SuccessResponse
{
    private bool $success = true;

    public function getSuccess(): bool
    {
        return $this->success;
    }
}