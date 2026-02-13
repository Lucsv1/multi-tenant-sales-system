<?php

namespace App\Application\Product\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'sku' => 'nullable|string|max:255',
      'price' => 'required|numeric|min:0',
      'cost' => 'nullable|numeric|min:0',
      'stock' => 'required|integer|min:0',
      'min_stock' => 'nullable|integer|min:0',
      'is_active' => 'boolean',
    ];
  }

}
