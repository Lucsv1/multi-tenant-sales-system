<?php

namespace App\Infra\Persistence\Eloquent\Models\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelHasPermission extends Model
{
    protected $fillable =
        [
            "permission_id",
            "model_type",
            "model_id"
        ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

}
