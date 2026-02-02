<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Experience;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user', name:'api_user_')]
class UserController extends AbstractController
{
    #[Route('/submit', name:'submit', methods:['POST'])]
    public function submit(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return $this->json(['error'=>'Niepoprawne dane JSON'],400);

        $user = new User();
        $user->setFirstName($data['basic']['firstName'] ?? null);
        $user->setLastName($data['basic']['lastName'] ?? null);
        $user->setBirthDate(isset($data['basic']['birthDate'])? new \DateTime($data['basic']['birthDate']):null);

        $contact = new Contact();
        $contact->setPhone($data['contact']['phone'] ?? null);
        $contact->setEmail($data['contact']['email'] ?? null);
        $contact->setUser($user);
        $user->setContact($contact);

        if (!empty($data['experience'])) {
            foreach ($data['experience'] as $expData) {
                $exp = new Experience();
                $exp->setCompany($expData['company'] ?? null);
                $exp->setPosition($expData['position'] ?? null);
                $exp->setDateFrom(isset($expData['dateFrom'])? new \DateTime($expData['dateFrom']):null);
                $exp->setDateTo(isset($expData['dateTo'])? new \DateTime($expData['dateTo']):null);
                $exp->setUser($user);
                $user->addExperience($exp);
            }
        }

        // Walidacja
        $errors = $validator->validate($user);
        $errors = array_merge(iterator_to_array($errors), iterator_to_array($validator->validate($contact)));
        foreach ($user->getExperiences() as $exp) $errors = array_merge($errors, iterator_to_array($validator->validate($exp)));

        if (count($errors) > 0) {
            $errMessages=[];
            foreach($errors as $e) $errMessages[]=$e->getPropertyPath().": ".$e->getMessage();
            return $this->json(['errors'=>$errMessages],400);
        }

        $em->persist($user);
        $em->persist($contact);
        foreach ($user->getExperiences() as $exp) $em->persist($exp);
        $em->flush();

        return $this->json([
            'basic'=>[
                'firstName'=>$user->getFirstName(),
                'lastName'=>$user->getLastName(),
                'birthDate'=>$user->getBirthDate()->format('Y-m-d')
            ],
            'contact'=>[
                'phone'=>$contact->getPhone(),
                'email'=>$contact->getEmail()
            ],
            'experience'=>array_map(fn($e)=>[
                'company'=>$e->getCompany(),
                'position'=>$e->getPosition(),
                'dateFrom'=>$e->getDateFrom()->format('Y-m-d'),
                'dateTo'=>$e->getDateTo()->format('Y-m-d')
            ], $user->getExperiences()->toArray())
        ]);
    }
}
