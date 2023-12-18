<?php

namespace App\Model;

use App\Entity\News;

class NewsDetailsResponse
{
    private int $id;
    private string $title;
    private string $content;
    private CategoriesListResponse $categories;
    private \DateTimeImmutable $publishedAt;

    public static function createByEntity(News $news): NewsDetailsResponse
    {
        return (new self)
            ->setId($news->getId())
            ->setTitle($news->getTitle())
            ->setContent($news->getContent())
            ->setCategories(CategoriesListResponse::createByCollection($news->getCategories()))
            ->setPublishedAt($news->getPublishedAt())
            ;
    }

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

    public function getCategories(): CategoriesListResponse
    {
        return $this->categories;
    }

    public function setCategories(CategoriesListResponse $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

}