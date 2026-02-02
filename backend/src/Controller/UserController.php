<?php

namespace App\Controller;

use App\Service\UserCreatorService;
use App\Service\UserValidationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/api/user", name: "api_user_")]
class UserController extends AbstractController
{
    #[Route("/submit", name: "submit", methods: ["POST", "OPTIONS"])]
    public function submit(
        Request $request,
        UserCreatorService $creator,
        UserValidationService $validator,
    ): JsonResponse {
        // Obsługa preflight (OPTIONS)
        if ($request->getMethod() === "OPTIONS") {
            return new JsonResponse(null, 200, [
                "Access-Control-Allow-Origin" => "http://localhost:5173",
                "Access-Control-Allow-Methods" => "POST, OPTIONS",
                "Access-Control-Allow-Headers" => "Content-Type, Authorization",
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(["error" => "Niepoprawny JSON"], 400, [
                "Access-Control-Allow-Origin" => "http://localhost:5173",
            ]);
        }

        $user = $creator->create($data);
        $errors = $validator->validate($user);

        if (!empty($errors)) {
            return new JsonResponse(
                [
                    "status" => "error",
                    "errors" => $errors,
                ],
                400,
                [
                    "Access-Control-Allow-Origin" => "http://localhost:5173",
                ],
            );
        }

        $creator->save($user);

        return new JsonResponse(
            [
                "status" => "success",
                "message" => "Użytkownik zapisany poprawnie",
            ],
            200,
            [
                "Access-Control-Allow-Origin" => "http://localhost:5173",
            ],
        );
    }
}
