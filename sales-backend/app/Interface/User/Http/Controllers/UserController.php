<?php

namespace App\Interface\User\Http\Controllers;

use App\Interface\Http\Controllers\Controller;
use App\Infra\User\Persistence\Eloquent\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index(): JsonResponse
    {
        $user = User::all();

        return response()->json([
            'message' => 'Usuario encontrado',
            'data' => $user,
        ]);

    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request): JsonResponse
    {

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

    }

    /**
     * Display the specified sale
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(
            ['data' => $user]
        );

    }

    public function update(Request $request, User $user): JsonResponse
    {

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

    }

    /**
     * Cancel a sale
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuario removido com sucesso',
        ]);

    }
}
