<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class StaffExpertise extends Model
{
    use HasFactory;

    protected $fillable = [
        'expertise_en',
        'expertise_idn'
    ];

    public function teachingStaffs(): BelongsToMany
    {
        return $this->belongsToMany(TeachingStaff::class, 'teaching_staff_staff_expertise');
    }
}
