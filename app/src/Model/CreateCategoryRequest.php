<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        private readonly string $name
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}