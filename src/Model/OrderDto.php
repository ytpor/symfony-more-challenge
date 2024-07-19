<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class OrderDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $user_id,

        #[Assert\All(
            new Assert\Collection(
                fields: [
                    'product_id' => [
                        new Assert\NotBlank,
                        new Assert\Positive
                    ],
                    'quantity' => [
                        new Assert\NotBlank,
                        new Assert\Positive
                    ],
                ],
            )
        )]
        public readonly array $products,
    )
    {
    }
}
