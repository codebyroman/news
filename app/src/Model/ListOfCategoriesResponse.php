<?php

namespace App\Model;

class ListOfCategoriesResponse
{
    private array $categories = [];

    public function addCategory(CategoryDetailsResponse $category): self
    {
        $this->categories[] = $category;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}