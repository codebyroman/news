<?php

namespace App\Model;

use App\Entity\News;
use Symfony\Component\Validator\Constraints as Assert;

class UserDetailsResponse
{
    private int $id;

    #[Assert\Email]
    #[Assert\Length(max: 255)]
    private string $email;

    private int $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

}