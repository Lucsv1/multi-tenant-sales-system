<?php

namespace App\Infra\Persistence\Eloquent\Models\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infra\Persistence\Eloquent\Models\Role\Role;

class ModelHasRole extends Model
{
    protected $fillable = [
        "role_id",
        "model_type",
        "model_id"
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

}
