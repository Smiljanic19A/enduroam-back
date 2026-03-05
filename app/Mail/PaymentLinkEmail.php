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

final class PaymentLinkEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $body;

    public ?string $ipsQrUrl;

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
        // If a custom message was provided by the admin, use it directly.
        // Otherwise load the default template from settings.
        if (!empty($this->customMessage)) {
            $this->body = $this->customMessage;
        } else {
            $template = SiteSetting::getValue('email_payment_link', '');

            $this->body = str_replace(
                ['{guest_name}', '{tour_name}', '{deposit_amount}', '{payment_link}'],
                [
                    $this->booking->guest_name,
                    $this->booking->bookable->name ?? '',
                    $this->booking->currency . number_format((float) ($this->booking->deposit_amount ?? $this->booking->total_price), 2),
                    $this->paymentLink,
                ],
                $template
            );
        }

        // Resolve IPS QR code URL
        $ipsPath = SiteSetting::getValue('payment_ips_qr_image');
        if ($ipsPath) {
            $this->ipsQrUrl = presigned_url($ipsPath);
        } else {
            // Fallback: use public QR from frontend
            $this->ipsQrUrl = rtrim(config('app.frontend_url', config('app.url')), '/') . '/qr.jpg';
        }

        $senderName = SiteSetting::getValue('email_sender_name', config('mail.from.name'));
        $this->from(config('mail.from.address'), $senderName);

        $replyTo = SiteSetting::getValue('email_reply_to');
        if ($replyTo) {
            $this->replyTo($replyTo, $senderName);
        }

        return new Content(
            view: 'emails.payment-link',
        );
    }
}
