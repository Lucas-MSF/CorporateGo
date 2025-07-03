<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\IncorrectCredentialsException;
use App\Http\Controllers\Controller;
use App\Interfaces\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
   public function __construct(private readonly AuthServiceInterface $authService)
   {
   }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $response = $this->authService->login($request->getUser(), $request->getPassword());
            return response()->json(['data' => $response], Response::HTTP_OK);
        } catch (IncorrectCredentialsException) {
            return response()->json(['message' => 'User and/or password incorrect'], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
