<?php

namespace App\Application\Sale\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'customer_id' => 'nullable|exists:customers,id',
      'discount' => 'nullable|numeric|min:0',
      'payment_method' => 'nullable|in:cash,credit_card,debit_card,pix,other',
      'notes' => 'nullable|string',
      'items' => 'required|array|min:1',
      'items.*.product_id' => 'required|exists:products,id',
      'items.*.quantity' => 'required|integer|min:1',
      'items.*.discount' => 'nullable|numeric|min:0',
    ];
  }

}
