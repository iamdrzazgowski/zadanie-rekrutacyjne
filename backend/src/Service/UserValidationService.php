<?php

namespace App\Service;

use App\Entity\User;
use App\Validator\UserValidator;
use App\Validator\ContactValidator;
use App\Validator\ExperienceValidator;

class UserValidationService
{
    public function __construct(
        private UserValidator $userValidator,
        private ContactValidator $contactValidator,
        private ExperienceValidator $experienceValidator
    ) {}

    public function validate(User $user): array
    {
        $errors = [];

        $errors = array_merge(
            $errors,
            $this->userValidator->validate($user)
        );

        if ($user->getContact()) {
            $errors = array_merge(
                $errors,
                $this->contactValidator->validate($user->getContact())
            );
        }

        foreach ($user->getExperiences() as $exp) {
            $errors = array_merge(
                $errors,
                $this->experienceValidator->validate($exp)
            );
        }

        return $errors;
    }
}
