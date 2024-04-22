<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Internship extends Model
{
    use HasFactory;

    protected $table = 'internships';

    protected $fillable = [
        'lecturer_id',
        'company_name',
        'location',
        'job_description',
        'supervisor_name',
        'supervisor_phone',
        'supervisor_email',
        'start_date',
        'end_date',
    ];
    
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
    public function logbooks(): MorphMany
    {
        return $this->morphMany(Logbook::class, 'commentable');
    }
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }
}
