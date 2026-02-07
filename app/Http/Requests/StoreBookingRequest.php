<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bookable_type' => ['required', 'in:tour,event'],
            'bookable_id' => ['required', 'integer'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:stripe,paypal,bank_transfer'],
        ];
    }
}
