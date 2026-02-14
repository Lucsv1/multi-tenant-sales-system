<?php
namespace App\Application\Tenant\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class TenantIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_by' => 'nullable|string|in:name,slug,email,cnpj,created_at,updated_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
