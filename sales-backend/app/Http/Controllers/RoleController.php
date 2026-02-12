<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index(): JsonResponse
    {
        $role = Role::all();

        return response()->json([
            'message' => 'Role encontrada',
            'data' => $role
        ]);
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255'
        ]);

        $role = Role::create($validated);

        return response()->json([
            'message' => 'Role criada com sucesso',
            'data' => $role
        ]);

    }

    /**
     * Display the specified sale
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role
        ]);

    }

    public function update(Request $request, Role $role): JsonResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255'
        ]);

        $role->update($validated);

        return response()->json([
            'message' => 'Role atualizada com sucesso',
            'data' => $role->fresh()
        ]);
    }

    /**
     * Cancel a sale
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([
            'message' => 'Role removido com sucesso'
        ]);

    }
}
