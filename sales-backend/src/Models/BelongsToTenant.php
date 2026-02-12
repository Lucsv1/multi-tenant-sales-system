<?php

namespace App\Traits;

use App\Models\Tenant;
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
      if (!$model->tenant_id && auth()->check()) {
        $model->tenant_id = auth()->user()->tenant_id;
      }
    });

    // Aplica filtro global para isolar por tenant
    static::addGlobalScope('tenant', function (Builder $builder) {
      if (auth()->check()) {
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