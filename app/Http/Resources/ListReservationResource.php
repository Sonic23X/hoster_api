<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListReservationResource extends JsonResource
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
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'stays' => 0,
            'nights' => 0,
            'special_dates' => 0,
            'season' => null,
        ];
    }
}
