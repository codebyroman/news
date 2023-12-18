<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateNewsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 255)]
        private readonly string $title,
        #[Assert\NotBlank]
        private readonly string $content,
        #[Assert\NotBlank]
        private readonly array $categoryIds
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }
}