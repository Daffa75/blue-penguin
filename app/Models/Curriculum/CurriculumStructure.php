<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CurriculumStructure extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'curriculum_structures';

    protected $fillable = [
        'curriculum_name',
        'website',
        'language',
    ];

    /**
     * Get the semesters associated with the curriculum structure.
     */
    public function semester(): HasMany
    {
        return $this->hasMany(Semester::class, 'curriculum_id');
    }
}
