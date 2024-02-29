<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
}
