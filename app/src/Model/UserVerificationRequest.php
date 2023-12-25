<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserVerificationRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(6)]
        private readonly string $code
    )
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}