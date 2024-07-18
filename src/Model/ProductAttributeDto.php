<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProductAttributeDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $product_id,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $attribute_id,
    )
    {
    }
}
