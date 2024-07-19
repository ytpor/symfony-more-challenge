<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProductRatingDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $product_id,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $rating,
    )
    {
    }
}
