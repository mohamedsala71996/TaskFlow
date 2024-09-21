<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'workspace_id' => $this->workspace_id,
            'id'        => $this->id,
            'name'      => $this->name,
            'photo'      => $this->photo,
            'lists_of_the_board'     => TheListResource::collection($this->whenLoaded('lists')),
            'users' => UserResource::collection($this->whenLoaded('users')), // Include boards
        ];
    }
}
