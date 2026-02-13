<?php

namespace App\Application\SaleItem\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaleItemIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'sale_id' => 'nullable|string',
            'product_id' => 'nullable|string',
            'sort_by' => 'nullable|string|in:product_name,price,quantity,subtotal,discount,total,created_at,updated_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
