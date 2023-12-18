<?php
namespace App\Exception;

class NoCategoriesWithGivenIdsForNews extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No categories with the given ids were found to create the news');
    }
}