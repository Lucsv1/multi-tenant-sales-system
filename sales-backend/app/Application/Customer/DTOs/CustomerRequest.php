<?php

namespace App\Application\Customer\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    if ($this->isMethod('GET')) {
      return [
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'cpf_cnpj' => 'nullable|string|max:20',
        'zip_code' => 'nullable|string|max:10',
        'address' => 'nullable|string|max:255',
        'number' => 'nullable|string|max:20',
        'complement' => 'nullable|string|max:100',
        'neighborhood' => 'nullable|string|max:100',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:2',
        'is_active' => 'boolean',
      ];
    }

    return [
      'name' => 'required|string|max:255',
      'email' => 'nullable|email',
      'phone' => 'nullable|string|max:20',
      'cpf_cnpj' => 'nullable|string|max:20',
      'zip_code' => 'nullable|string|max:10',
      'address' => 'nullable|string|max:255',
      'number' => 'nullable|string|max:20',
      'complement' => 'nullable|string|max:100',
      'neighborhood' => 'nullable|string|max:100',
      'city' => 'nullable|string|max:100',
      'state' => 'nullable|string|max:2',
      'is_active' => 'boolean',
    ];
  }

}
