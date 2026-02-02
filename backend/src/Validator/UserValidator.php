<?php

namespace App\Validator;

use App\Entity\User;

class UserValidator
{
    public function validate(User $user): array
    {
        $errors = [];

        if (empty($user->getFirstName())) {
            $errors[] = 'Imię jest wymagane';
        }

        if (empty($user->getLastName())) {
            $errors[] = 'Nazwisko jest wymagane';
        }

        if (!$user->getBirthDate()) {
            $errors[] = 'Data urodzenia jest wymagana';
        } elseif ($user->getBirthDate() >= new \DateTime()) {
            $errors[] = 'Data urodzenia musi być w przeszłości';
        }

        return $errors;
    }
}
