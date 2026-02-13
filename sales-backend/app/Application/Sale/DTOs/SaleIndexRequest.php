<?php

namespace App\Application\Sale\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class SaleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,cancelled,refunded',
            'payment_method' => 'nullable|string|in:cash,credit_card,debit_card,pix,transfer',
            'customer_id' => 'nullable|string',
            'user_id' => 'nullable|string',
            'sort_by' => 'nullable|string|in:sale_number,total,discount,subtotal,status,sale_date,created_at,updated_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
