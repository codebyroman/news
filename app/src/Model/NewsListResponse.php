<?php

namespace App\Model;

class NewsListResponse
{
    /**
     * @var NewsDetailsResponse[]
     */
    private array $items;

    private int $page;

    private int $pages;

    private int $perPage;

    private int $total;

    /**
     * @return NewsDetailsResponse[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param NewsDetailsResponse[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }
}