<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class BookingStatusUpdate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $body;

    public function __construct(
        public readonly Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        $replyTo = SiteSetting::getValue('email_reply_to');

        return new Envelope(
            subject: "Booking #{$this->booking->id} — Status Updated",
            replyTo: $replyTo ? [$replyTo] : [],
        );
    }

    public function content(): Content
    {
        $settingKey = match ($this->booking->status) {
            'approved' => 'email_booking_approved',
            'declined' => 'email_booking_declined',
            'cancelled' => 'email_booking_cancelled',
            default => 'email_booking_approved',
        };

        $template = SiteSetting::getValue($settingKey, '');

        $paymentLink = SiteSetting::getValue('payment_paypal_link', '');

        $this->body = str_replace(
            ['{guest_name}', '{tour_name}', '{booking_date}', '{guests}', '{total_price}', '{deposit_amount}', '{payment_link}'],
            [
                $this->booking->guest_name,
                $this->booking->bookable->name ?? '',
                $this->booking->start_date->format('l, F j, Y'),
                (string) $this->booking->number_of_guests,
                $this->booking->currency . number_format((float) $this->booking->total_price, 2),
                $this->booking->currency . number_format((float) ($this->booking->deposit_amount ?? $this->booking->total_price), 2),
                $paymentLink,
            ],
            $template
        );

        $senderName = SiteSetting::getValue('email_sender_name', config('mail.from.name'));
        $this->from(config('mail.from.address'), $senderName);

        return new Content(
            view: 'emails.booking-status-update',
        );
    }
}
