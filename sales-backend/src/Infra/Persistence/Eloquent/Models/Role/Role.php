<?php

namespace App\Infra\Persistence\Eloquent\Models\Role;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infra\Persistence\Eloquent\Models\User\User;

class Role extends Model
{

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
