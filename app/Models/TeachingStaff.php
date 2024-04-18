<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeachingStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
        'role_id',
        'expertise_en',
        'expertise_idn',
        'link',
        'email'
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class);
    }
}
