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

}