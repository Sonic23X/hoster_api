<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'property' => new PropertyResource($this->property),
            'created_at' => $this->created_at,
            'confirmation_code' => $this->confirmation_code,
            'status' => $this->status,
            'cancel_date' => $this->cancel_date,
            'stays' => 0,
            'nights' => 0,
        ];
    }
}
