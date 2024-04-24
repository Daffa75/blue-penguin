<?php
namespace App\Filament\Admin\Resources\StructureResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class StructureTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'structure_id' => $this->id,
            'curriculum_name' => $this->curriculum_name,
            'image' => $this->media->pluck('original_url')->first(),
            'semester' => $this->semester->map(function ($semester) {
                return [
                    'id' => $semester->id,
                    'semester_name' => $semester->semester_name,
                    'credit_total' => $semester->credit_total,
                    'modules' => $semester->modules->map(function ($module) {
                        return [
                            'id' => $module->id,
                            'module_code' => $module->module_code,
                            'module_name' => $module->module_name,
                            'credit_points' => $module->credit_points,
                            'module_handbook' => $module->module_handbook
                        ];
                    })
                ];
            })
        ];
    }
}
