<?php

namespace App\Application\Tenant\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'slug' => 'required|string|max:255|unique:tenants,slug',
      'email' => 'required|email',
      'phone' => 'nullable|string|max:20',
      'cnpj' => 'required|string|max:20',
      'is_active' => 'boolean',
    ];
  }

}
