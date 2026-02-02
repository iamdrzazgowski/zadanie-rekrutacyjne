<?php

namespace App\Validator;

use App\Entity\Contact;

class ContactValidator
{
    public function validate(Contact $contact): array
    {
        $errors = [];

        if (empty($contact->getPhone())) {
            $errors[] = 'Telefon jest wymagany';
        } elseif (!preg_match('/^\+?[0-9]{9,15}$/', $contact->getPhone())) {
            $errors[] = 'Niepoprawny numer telefonu';
        }

        if (empty($contact->getEmail())) {
            $errors[] = 'Email jest wymagany';
        } elseif (!filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Niepoprawny adres email';
        }

        return $errors;
    }
}
