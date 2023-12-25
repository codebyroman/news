<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserRequest
{
    public function __construct(
        #[NotBlank]
        public string $name,

        #[Email]
        #[NotBlank]
        public string $email,

        #[NotBlank]
        #[Length(min: 8)]
        public string $password,

        #[NotBlank]
        #[EqualTo(propertyPath: 'password', message: 'This value should be equal to password field')]
        public string $confirmPassword,
    )
    {
    }
}
