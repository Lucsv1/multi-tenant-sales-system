<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
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
