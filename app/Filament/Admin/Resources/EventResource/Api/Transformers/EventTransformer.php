<?php
namespace App\Filament\Admin\Resources\EventResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EventTransformer extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'article' => $this->description,
            'date' => $this->date,
            'author' => $this->author,
            'language' => $this->language,
        ];
    }
}
