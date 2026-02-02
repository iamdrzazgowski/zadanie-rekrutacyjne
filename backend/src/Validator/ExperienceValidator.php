<?php

namespace App\Validator;

use App\Entity\Experience;

class ExperienceValidator
{
    public function validate(Experience $exp): array
    {
        $errors = [];

        if (empty($exp->getCompany())) {
            $errors[] = 'Firma jest wymagana';
        }

        if (empty($exp->getPosition())) {
            $errors[] = 'Stanowisko jest wymagane';
        }

        if (!$exp->getDateFrom() || !$exp->getDateTo()) {
            $errors[] = 'Daty doświadczenia są wymagane';
        } elseif ($exp->getDateFrom() > $exp->getDateTo()) {
            $errors[] = 'Data od nie może być późniejsza niż data do';
        }

        return $errors;
    }
}
