<?php

namespace App\Infra\Persistence\Eloquent\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'cnpj',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(\App\Infra\Persistence\Eloquent\Models\User\User::class);
    }

    /**
     * Relacionamento com Products
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Infra\Persistence\Eloquent\Models\Product\Product::class);
    }

    /**
     * Relacionamento com Customers
     */
    public function customers(): HasMany
    {
        return $this->hasMany(\App\Infra\Persistence\Eloquent\Models\Customer\Customer::class);
    }

    /**
     * Relacionamento com Sales
     */
    public function sales(): HasMany
    {
        return $this->hasMany(\App\Infra\Persistence\Eloquent\Models\Sale\Sale::class);
    }
}