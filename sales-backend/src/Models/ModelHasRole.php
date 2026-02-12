<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Role;
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
