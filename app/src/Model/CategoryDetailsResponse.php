<?php

namespace App\Model;

use App\Entity\Category;

class CategoryDetailsResponse
{
    private string $id;
    private string $name;

    public static function createByEntity(Category $category)
    {
        return (new self())
            ->setId($category->getId())
            ->setName($category->getName());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}