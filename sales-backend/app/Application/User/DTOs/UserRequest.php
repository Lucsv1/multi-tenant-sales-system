<?php

namespace App\Application\User\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')
          ->where('tenant_id', auth()->user()->tenant_id)
      ],
      'password' => 'required|string|min:8|confirmed',
      'role' => 'nullable|string|exists:roles,name'
    ];
  }

  public function messages(): array
  {
    return [
      'email.unique' => 'Este email já está em uso neste estabelecimento.',
    ];
  }

}