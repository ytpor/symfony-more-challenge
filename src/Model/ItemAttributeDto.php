<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ItemAttributeDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly string $description,
    )
    {
    }
}
