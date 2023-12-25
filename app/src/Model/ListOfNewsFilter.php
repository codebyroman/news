<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;


class ListOfNewsFilter
{
    public function __construct(
        public readonly ?int $id,

        #[Assert\Email]
        #[Assert\Length(max: 255)]
        public readonly ?string $title,
    ) {}
}