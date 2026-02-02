<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Experience;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCreatorService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function create(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['basic']['firstName']);
        $user->setLastName($data['basic']['lastName']);
        $user->setBirthDate(new \DateTime($data['basic']['birthDate']));

        $contact = new Contact();
        $contact->setPhone($data['contact']['phone']);
        $contact->setEmail($data['contact']['email']);
        $contact->setUser($user);
        $user->setContact($contact);

        foreach ($data['experience'] as $row) {
            $exp = new Experience();
            $exp->setCompany($row['company']);
            $exp->setPosition($row['position']);
            $exp->setDateFrom(new \DateTime($row['dateFrom']));
            $exp->setDateTo(new \DateTime($row['dateTo']));
            $exp->setUser($user);

            $user->getExperiences()->add($exp);
        }

        return $user;
    }

    public function validate(User $user): array
    {
        $errors = $this->validator->validate($user);

        return array_map(
            fn($e) => $e->getPropertyPath() . ': ' . $e->getMessage(),
            iterator_to_array($errors)
        );
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
