<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'the_list_id' => $this->the_list_id,
            'id' => $this->id,
            'text' => $this->text,
            'completed' => $this->completed,
            'position' => $this->position,
            // 'files' =>FileResource::collection($this->whenLoaded('files')),

        ];
    }
}
