<?php

namespace App\Model;

use Doctrine\Common\Collections\Collection;

class CategoriesListResponse
{
    /**
     * @param CategoryDetailsResponse[] $items
     */
    public function __construct(private readonly array $items)
    {
    }

    public static function createByCollection(Collection $collection): CategoriesListResponse
    {
        $items = [];

        foreach ($collection as $category) {
            $items[] = CategoryDetailsResponse::createByEntity($category);
        }

        return new self($items);
    }

    /**
     * @return CategoryDetailsResponse[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
