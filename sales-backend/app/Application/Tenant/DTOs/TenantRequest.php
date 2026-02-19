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
    $tenantId = $this->route('tenant')?->id;

    return [
      'name' => 'required|string|max:255',
      'slug' => 'required|string|max:255|unique:tenants,slug,' . ($tenantId ?? ''),
      'email' => 'required|email|unique:tenants,email,' . ($tenantId ?? ''),
      'phone' => 'nullable|string|max:20',
      'cnpj' => 'required|string|max:20|unique:tenants,cnpj,' . ($tenantId ?? ''),
      'is_active' => 'boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'slug.unique' => 'Este slug já está em uso por outro estabelecimento.',
      'email.unique' => 'Este email já está em uso por outro estabelecimento.',
      'cnpj.unique' => 'Este CNPJ já está cadastrado no sistema.',
    ];
  }

}
