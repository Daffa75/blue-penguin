<?php
namespace App\Filament\Admin\Resources\AdministrativeStaffResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AdministrativeStaffTransformer extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_en' => $this->role->role_en,
            'role_idn' => $this->role->role_idn,
            'image' => $this->media->pluck('original_url')->first(),
        ];
    }
}
