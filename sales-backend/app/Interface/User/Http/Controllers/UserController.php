<?php

namespace App\Interface\User\Http\Controllers;

use App\Interface\Shared\Http\Controllers\Controller;
use App\Infra\User\Persistence\Eloquent\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = User::all();
            return response()->json([
                'message' => 'Usuario encontrado',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar usuarios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create($validated);

            return response()->json([
                'message' => 'Usuario criado com sucesso',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar usuario',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(User $user): JsonResponse
    {
        try {
            return response()->json(['data' => $user]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Usuario atualizado com sucesso',
                'data' => $user->fresh(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuario',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'Usuario removido com sucesso',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
