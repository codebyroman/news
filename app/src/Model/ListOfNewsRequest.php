<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ListOfNewsRequest
{
    public function __construct(
        #[Assert\Valid]
        public readonly ?ListOfNewsFilter $filter = null,

        public readonly ?int $page = 1,
    ) {}
}