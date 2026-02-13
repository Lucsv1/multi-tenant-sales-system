<?php

namespace App\Infra\User\Persistence\Eloquent;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'users';
    protected $guard_name = 'api';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }


    /**
     * Relacionamento com Sales (vendas realizadas)
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Verificar se é admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin da Loja');
    }

}