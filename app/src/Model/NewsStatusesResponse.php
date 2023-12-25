<?php

namespace App\Model;

use App\Entity\News;
use OpenApi\Attributes as OA;
class NewsStatusesResponse
{
    public function __construct(
        private readonly array $statuses
    )
    {
    }


    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public function getStatuses(): array
    {
        return $this->statuses;
    }
}