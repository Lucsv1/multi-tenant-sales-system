<?php

namespace App\Infra\Persistence\Eloquent\Traits;

use App\Infra\Tenant\Persistence\Eloquent\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
  /**
   * Boot the trait.
   */
  protected static function bootBelongsToTenant(): void
  {
    // Adiciona o tenant_id automaticamente ao criar
    static::creating(function ($model) {
      if (!$model->tenant_id && auth()->check() && auth()->user()->tenant_id) {
        $model->tenant_id = auth()->user()->tenant_id;
      }
    });

    // Aplica filtro global para isolar por tenant (não aplica para SuperAdmin)
    static::addGlobalScope('tenant', function (Builder $builder) {
      if (auth()->check() && auth()->user()->tenant_id && !auth()->user()->isSuperAdmin()) {
        $builder->where('tenant_id', auth()->user()->tenant_id);
      }
    });
  }

  /**
   * Relacionamento com Tenant
   */
  public function tenant(): BelongsTo
  {
    return $this->belongsTo(Tenant::class);
  }
}