<?php
namespace App\Filament\Admin\Resources\LaboratoryResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $mediaUrls = $this->media->pluck('original_url')->toArray();

        $studentsData = $this->students->groupBy(function($student) {
            return $student->pivot->role;
        })->map(function($group) {
            return $group->map(function($student) {
                return [
                    'name' => $student->name,
                    'nim' => $student->nim,
                ];
            });
        });

        $lecturersData = $this->lecturers->groupBy(function($lecturer) {
            return $lecturer->pivot->role;
        })->map(function($group) {
            return $group->map(function($lecturer) {
                return [
                    'name' => $lecturer->name,
                    'nim' => $lecturer->nip,
                    'image_url' => $lecturer->image_url
                ];
            });
        });

        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_id' => $this->name_id,
            'description_en' => $this->description_en,
            'description_id' => $this->description_id,
            'media' => $mediaUrls,
            'lecturers' => $lecturersData,
            'students' => $studentsData,
        ];
    }
}
