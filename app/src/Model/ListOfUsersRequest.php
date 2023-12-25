<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ListOfUsersRequest
{
    public function __construct(
        #[Assert\Valid]
        public readonly ?ListOfUsersFilter $filter = null,

        public readonly ?int $page = 1,
    ) {}
}