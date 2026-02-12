<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  /**
   * Register a new user
   */
  public function register(Request $request): JsonResponse
  {
    $request->validate([
      'tenant_id' => 'required|exists:tenants,id',
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255',
      'password' => 'required|string|min:8|confirmed',
    ]);

    // Verificar se email já existe para este tenant
    $exists = User::where('tenant_id', $request->tenant_id)
      ->where('email', $request->email)
      ->exists();

    if ($exists) {
      throw ValidationException::withMessages([
        'email' => ['Este email já está em uso neste estabelecimento.'],
      ]);
    }

    $user = User::create([
      'tenant_id' => $request->tenant_id,
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    // Atribuir role padrão de Vendedor
    $user->HasRole('Vendedor');

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Usuário registrado com sucesso',
      'user' => $user->load('roles'),
      'token' => $token,
      'token_type' => 'Bearer',
    ], 201);
  }

  /**
   * Login user
   */
  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['As credenciais fornecidas estão incorretas.'],
      ]);
    }

    if (!$user->is_active) {
      throw ValidationException::withMessages([
        'email' => ['Esta conta está inativa.'],
      ]);
    }

    // Revogar tokens antigos
    $user->tokens()->delete();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Login realizado com sucesso',
      'user' => $user->load(['roles', 'tenant']),
      'token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

  /**
   * Logout user
   */
  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Logout realizado com sucesso',
    ]);
  }

  /**
   * Get authenticated user
   */
  public function me(Request $request): JsonResponse
  {
    return response()->json([
      'user' => $request->user()->load(['roles', 'tenant']),
    ]);
  }
}