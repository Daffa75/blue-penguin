<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Laboratory extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name_en',
        'name_id',
        'description_en',
        'description_id',
    ];

    public function members(): BelongsToMany {
        return $this->belongsToMany(Student::class)->withPivot('role');
    }

    public function lecturers(): BelongsToMany {
        return $this->belongsToMany(Lecturer::class)->withPivot('role');
    }
    public function students(): BelongsToMany {
        return $this->belongsToMany(Student::class)->withPivot('role');
    }
}
