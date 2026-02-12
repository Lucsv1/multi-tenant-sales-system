<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Permission;
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
