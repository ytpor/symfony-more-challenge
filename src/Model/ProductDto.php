<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $category_id,

        #[Assert\NotBlank]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly string $description,

        #[Assert\NotBlank]
        public readonly string $brand,

        #[Assert\NotBlank]
        public readonly string $model,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $unit_price,
    )
    {
    }
}
