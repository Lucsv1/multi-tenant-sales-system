<?php

namespace App\Application\SaleItem\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaleItemRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'sale_id' => 'required|exists:sales,id',
      'product_id' => 'required|exists:products,id',
      'product_name' => 'required|string|max:255',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:1',
      'discount' => 'nullable|numeric|min:0',
    ];
  }

}
