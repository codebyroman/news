<?php

namespace App\Model;

use App\Entity\Category;
use App\Entity\News;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;

class NewsDetailsResponse
{
    private int $id;
    private string $title;
    private string $content;

    #[OA\Property(property: 'categories', type: 'array', items: new OA\Items(type: CategoryDetailsResponse::class))]
    private array $categories;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

}