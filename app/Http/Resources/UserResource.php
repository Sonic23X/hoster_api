<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'email' => $this->email,
            'phone' => $this->telephone,
            'avatar' => 'assets/images/avatars/brian-hughes.jpg',
            'type' => $this->roles->first()->name,
            'is_partner' => $this->is_partner,
            'properties' => $this->properties->count(),
        ];
    }
}
