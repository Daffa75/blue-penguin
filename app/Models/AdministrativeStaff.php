<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdministrativeStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'expertise',
        'link'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class);
    }
}

