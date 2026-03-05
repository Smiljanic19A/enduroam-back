<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class NewsletterBroadcast extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        string $subject,
        public readonly string $body,
        public readonly string $unsubscribeUrl
    ) {
        $this->subject = $subject;
    }

    public function envelope(): Envelope
    {
        $replyTo = \App\Models\SiteSetting::getValue('email_reply_to');

        return new Envelope(
            subject: $this->subject,
            replyTo: $replyTo ? [$replyTo] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'subject' => $this->subject,
            ],
        );
    }
}
