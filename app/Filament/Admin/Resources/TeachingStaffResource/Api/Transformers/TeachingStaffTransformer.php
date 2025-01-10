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
            'email' => $this->email,
            'concentration' => $this->concentration,
            'role_en' => $this->role->role_en,
            'role_idn' => $this->role->role_idn,
            'expertise_idn' => $this->staffExpertise->pluck('expertise_idn')->toArray(),
            'expertise_en' => $this->staffExpertise->pluck('expertise_en')->toArray(),
            'handbook_link' => $this->handbook_link,
            'scholar_link' => $this->scholar_link,
            'scopus_link' => $this->scopus_link,
            'image' => $this->lecturer->image_url,
            'publications' => $this->lecturer->publications->map(function ($publication) {
                return [
                    'title' => $publication->title,
                    'link' => $publication->link,
                    'year' => $publication->year,
                    'type' => $publication->type,
                ];
            }),
        ];
    }
}
