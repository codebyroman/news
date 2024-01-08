<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_AUTHOR = 'ROLE_AUTHOR';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';

    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;
    const STATUS_INACTIVE = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[JoinTable(name: 'category_moderators')]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'moderators')]
    private Collection $categoriesToModerate;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: News::class)]
    private Collection $writtenNews;

    #[ORM\OneToMany(mappedBy: 'moderator', targetEntity: News::class)]
    private Collection $newsToModerate;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    public function __construct()
    {
        $this->categoriesToModerate = new ArrayCollection();
        $this->writtenNews = new ArrayCollection();
        $this->newsToModerate = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoriesToModerate(): Collection
    {
        return $this->categoriesToModerate;
    }

    public function addCategoryToModerate(Category $moderatesCategory): static
    {
        if (!$this->categoriesToModerate->contains($moderatesCategory)) {
            $this->categoriesToModerate->add($moderatesCategory);
        }

        return $this;
    }

    public function removeCategoryToModerate(Category $moderatesCategory): static
    {
        $this->categoriesToModerate->removeElement($moderatesCategory);

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getWrittenNews(): Collection
    {
        return $this->writtenNews;
    }

    public function addWrittenNews(News $writtenNews): static
    {
        if (!$this->writtenNews->contains($writtenNews)) {
            $this->writtenNews->add($writtenNews);
            $writtenNews->setAuthor($this);
        }

        return $this;
    }

    public function removeWrittenNews(News $writtenNews): static
    {
        if ($this->writtenNews->removeElement($writtenNews)) {
            // set the owning side to null (unless already changed)
            if ($writtenNews->getAuthor() === $this) {
                $writtenNews->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNewsToModerate(): Collection
    {
        return $this->newsToModerate;
    }

    public function addNewsToModerate(News $newsToModerate): static
    {
        if (!$this->newsToModerate->contains($newsToModerate)) {
            $this->newsToModerate->add($newsToModerate);
            $newsToModerate->setModerator($this);
        }

        return $this;
    }

    public function removeNewsToModerate(News $newsToModerate): static
    {
        if ($this->newsToModerate->removeElement($newsToModerate)) {
            // set the owning side to null (unless already changed)
            if ($newsToModerate->getModerator() === $this) {
                $newsToModerate->setModerator(null);
            }
        }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isModerator(): bool
    {
        return in_array(self::ROLE_MODERATOR, $this->getRoles());
    }

    public function isAuthor(): bool
    {
        return in_array(self::ROLE_AUTHOR, $this->getRoles());
    }

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }

    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    public function isBanned(): bool
    {
        return $this->getStatus() === self::STATUS_BANNED;
    }
}
