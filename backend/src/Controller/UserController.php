<?php

namespace App\Controller;

use App\Service\UserCreatorService;
use App\Service\UserValidationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    #[Route('/submit', name: 'submit', methods: ['POST'])]
    public function submit(
        Request $request,
        UserCreatorService $creator,
        UserValidationService $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Niepoprawny JSON'], 400);
        }

        $user = $creator->create($data);
        $errors = $validator->validate($user);

        if (!empty($errors)) {
            return $this->json([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        $creator->save($user);

        return $this->json([
            'status' => 'success',
            'message' => 'Użytkownik zapisany poprawnie'
        ], 200);
    }
}
