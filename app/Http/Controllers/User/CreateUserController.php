<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateUserController extends Controller
{
    public function __construct(private readonly UserServiceInterface $userService)
    {
    }

    public function __invoke(CreateUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());
            return response()->json([
                'message' => 'User Created Successfully!',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Internal Server Error!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
