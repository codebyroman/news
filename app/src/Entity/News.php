<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    const STATUS_ACTIVE = 1;
    const STATUS_TO_MODERATE = 2;
    const STATUS_REJECTED = 3;
    const STATUS_BANNED = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'writtenNews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $author = null;

    #[ORM\ManyToOne(inversedBy: 'newsToModerate')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $moderator = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'news')]
    private Collection $categories;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $moderatedAt = null;

    private ?bool $needToNotifyAboutStatus = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getModerator(): ?UserInterface
    {
        return $this->moderator;
    }

    public function setModerator(?UserInterface $moderator): static
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModeratedAt(): ?\DateTimeImmutable
    {
        return $this->moderatedAt;
    }

    public function setModeratedAt(?\DateTimeImmutable $moderatedAt): static
    {
        $this->moderatedAt = $moderatedAt;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    public function isModerating(): bool
    {
        return $this->getStatus() === self::STATUS_TO_MODERATE;
    }

    public function isRejected(): bool
    {
        return $this->getStatus() === self::STATUS_REJECTED;
    }

    public function isBanned(): bool
    {
        return $this->getStatus() === self::STATUS_BANNED;
    }

    public function getUrl(): string
    {
        // TODO generate the url to see this news on the website
        return "URL TO THE PAGE ID {$this->getId()}";
    }

    public function isNeedToNotifyAboutStatus(): ?bool
    {
        return $this->needToNotifyAboutStatus;
    }

    public function setNeedToNotifyAboutStatus(bool $needToNotifyAboutStatus): self
    {
        $this->needToNotifyAboutStatus = $needToNotifyAboutStatus;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
