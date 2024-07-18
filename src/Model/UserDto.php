<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $email,

        #[Assert\NotBlank]
        public readonly string $password,

        #[Assert\NotBlank]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly string $phone,

        #[Assert\NotBlank]
        public readonly string $address,
    )
    {
    }
}
