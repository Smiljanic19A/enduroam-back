<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bookableType' => class_basename($this->bookable_type),
            'bookableId' => $this->bookable_id,
            'bookable' => $this->whenLoaded('bookable', function () {
                return [
                    'id' => $this->bookable->id,
                    'name' => $this->bookable->name,
                ];
            }),
            'startDate' => $this->start_date?->format('Y-m-d'),
            'guestName' => $this->guest_name,
            'guestEmail' => $this->guest_email,
            'guestPhone' => $this->guest_phone,
            'numberOfGuests' => $this->number_of_guests,
            'specialRequests' => $this->special_requests,
            'paymentMethod' => $this->payment_method,
            'status' => $this->status,
            'totalPrice' => (float) $this->total_price,
            'currency' => $this->currency,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
