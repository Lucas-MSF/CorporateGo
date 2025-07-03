<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function __construct(private readonly AuthServiceInterface $authService)
    {
    }

    public function __invoke(): Response | JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
