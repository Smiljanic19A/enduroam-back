<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AdminQuickEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $emailSubject,
        public readonly string $body
    ) {}

    public function envelope(): Envelope
    {
        $replyTo = SiteSetting::getValue('email_reply_to');

        return new Envelope(
            subject: $this->emailSubject,
            replyTo: $replyTo ? [$replyTo] : [],
        );
    }

    public function content(): Content
    {
        $senderName = SiteSetting::getValue('email_sender_name', config('mail.from.name'));
        $this->from(config('mail.from.address'), $senderName);

        return new Content(
            view: 'emails.quick-email',
        );
    }
}
