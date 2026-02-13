<?php

namespace App\Infra\Customer\Persistence\Eloquent;

use App\Infra\Persistence\Eloquent\Traits\BelongsToTenant;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'cpf_cnpj',
        'zip_code',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com Sales
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Accessor para formatar CPF/CNPJ
     */
    public function getFormattedCpfCnpjAttribute(): ?string
    {
        if (!$this->cpf_cnpj) {
            return null;
        }

        $cpfCnpj = preg_replace('/\D/', '', $this->cpf_cnpj);

        if (strlen($cpfCnpj) === 11) {
            // CPF
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            // CNPJ
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpfCnpj);
        }

        return $this->cpf_cnpj;
    }
}