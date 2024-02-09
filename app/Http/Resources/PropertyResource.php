<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = collect();
        foreach ($this->images as $image) {
            $images->push([
                'uuid' => $image->uuid,
                'name' => $image->name,
                'url' => url('') . '/storage/' . $image->storage
            ]);
        }

        if ($images->isEmpty()) {
            $images->push([
                'uuid' => 'no-image',
                'name' => 'No image',
                'url' => 'assets/images/ui/angular-material/scenes/badge.scene.png'
            ]);
        }

        return [
            'id' => $this->uuid,
            'owner' => [
                'id' => $this->owner->uuid,
                'name' => $this->owner->name . ' ' . $this->owner->last_name . ' ' . $this->owner->second_last_name,
                'email' => $this->owner->email,
                'telephone' => $this->owner->telephone,
                'avatar' => $this->owner->avatar
            ],
            'title' => $this->title,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'rooms' => $this->rooms,
            'beds' => $this->beds,
            'bathrooms' => $this->bathrooms,
            'about' => $this->about,
            'additional_information' => $this->additional_information,
            'security' => $this->security,
            'arrive' => $this->arrive,
            'images' => $images,
            'services' => $this->services->sortBy('order')->toArray()
        ];
    }
}
