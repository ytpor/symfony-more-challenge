<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class OrderUpdateDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $status,
    )
    {
    }
}
