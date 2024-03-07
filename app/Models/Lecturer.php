<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lecturer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'image_url',
        'nip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class);
    }
    public function finalProjects(): BelongsToMany
    {
        return $this->belongsToMany(FinalProject::class)->withPivot('role');
    }
    public function hakis(): BelongsToMany
    {
        return $this->belongsToMany(Haki::class);
    }
    public function laboratories(): BelongsToMany
    {
        return $this->belongsToMany(Laboratory::class)->withPivot('role');
    }
    // public function teachingStaff(): BelongsTo
    // {
    //     return $this->belongsTo(TeachingStaff::class);
    // }
}
