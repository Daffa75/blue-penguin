<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_en',
        'role_id'
    ];

    // public function teachingStaff(): BelongsTo
    // {
    //     return $this->belongsTo(TeachingStaff::class);
    // }
}
