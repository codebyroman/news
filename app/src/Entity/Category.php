<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'moderatesCategories')]
    private Collection $moderators;

    #[ORM\ManyToMany(targetEntity: News::class, mappedBy: 'categories')]
    private Collection $news;

    public function __construct()
    {
        $this->moderators = new ArrayCollection();
        $this->news = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getModerators(): Collection
    {
        return $this->moderators;
    }

    public function addModerator(User $moderator): static
    {
        if (!$this->moderators->contains($moderator)) {
            $this->moderators->add($moderator);
            $moderator->addCategoryToModerate($this);
        }

        return $this;
    }

    public function removeModerator(User $moderator): static
    {
        if ($this->moderators->removeElement($moderator)) {
            $moderator->removeCategoryToModerate($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->addCategory($this);
        }

        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            $news->removeCategory($this);
        }

        return $this;
    }
}
