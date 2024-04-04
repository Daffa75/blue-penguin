<?php
namespace App\Filament\Admin\Resources\TeachingStaffResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachingStaffTransformer extends JsonResource
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
            'name' => $this->lecturer->name,
            'nip' => $this->lecturer->nip,
            'role_en' => $this->role->role_en,
            'role_idn' => $this->role->role_idn,
            'expertise_en' => $this->expertise_en,
            'expertise_idn' => $this->expertise_idn,
            'link' => $this->link,
            'image' => $this->lecturer->image_url,
        ];
    }
}
