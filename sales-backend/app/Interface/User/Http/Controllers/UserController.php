<?php

namespace App\Interface\User\Http\Controllers;


use App\Application\User\DTOs\UserIndexRequest;
use App\Application\User\DTOs\UserRequest;
use App\Application\User\Service\UserService;
use App\Infra\User\Persistence\Eloquent\User;
use App\Interface\Shared\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(UserIndexRequest $userRequest): JsonResponse
    {
        try {
            $user = $this->userService->index($userRequest);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar usuarios: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(UserRequest $userRequest): JsonResponse
    {
        try {
            $user = $this->userService->store($userRequest);
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar usuario: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(User $user): JsonResponse
    {
        try {
            $user = $this->userService->show($user);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar usuario: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UserRequest $userRequest, User $user): JsonResponse
    {
        try {
            $user = $this->userService->update($userRequest, $user);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuario: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user = $this->userService->destroy($user);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover usuario: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
