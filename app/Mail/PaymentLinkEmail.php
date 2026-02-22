<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class PaymentLinkEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly string $customMessage,
        public readonly string $paymentLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment for Booking #{$this->booking->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-link',
        );
    }
}
