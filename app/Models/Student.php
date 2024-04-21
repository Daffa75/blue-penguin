<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nim',
        'email',
        'phone_number',
    ];

    public function finalProject(): HasOne
    {
        return $this->hasOne(FinalProject::class);
    }
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class);
    }
    public function laboratories(): BelongsToMany
    {
        return $this->belongsToMany(Laboratory::class)->withPivot('role');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function internships(): BelongsToMany
    {
        return $this->belongsToMany(Internship::class);
    }
    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class, 'student_id');
    }
}
