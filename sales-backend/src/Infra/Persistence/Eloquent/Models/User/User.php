<?php

namespace App\Infra\Persistence\Eloquent\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Infra\Persistence\Eloquent\Models\Role;
use App\Infra\Persistence\Eloquent\Models\Sale\Sale;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

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
        return $this->belongsTo(\App\Infra\Persistence\Eloquent\Models\Tenant\Tenant::class);
    }


    /**
     * Relacionamento com Sales (vendas realizadas)
     */
    public function sales(): HasMany
    {
        return $this->hasMany(\App\Infra\Persistence\Eloquent\Models\Sale\Sale::class);
    }

    /**
     * Verificar se usuário tem uma role específica
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Verificar se é admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin da Loja');
    }

}