<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bookable_type',
        'bookable_id',
        'start_date',
        'guest_name',
        'guest_email',
        'guest_phone',
        'number_of_guests',
        'special_requests',
        'payment_method',
        'status',
        'total_price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'number_of_guests' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
