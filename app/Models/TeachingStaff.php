<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeachingStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
        'role_id',
        'concentration',
        'email',
        'handbook_link',
        'scholar_link',
        'scopus_link',
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class);
    }

    public function staffExpertise(): BelongsToMany
    {
        return $this->belongsToMany(StaffExpertise::class, 'teaching_staff_staff_expertise');
    }
}
