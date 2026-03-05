<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ContactMessageReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $body;

    public function __construct(
        public readonly ContactMessage $contactMessage,
        public readonly string $replyMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Re: {$this->contactMessage->subject}",
        );
    }

    public function content(): Content
    {
        // The admin always writes a custom reply, so use replyMessage directly
        $this->body = $this->replyMessage;

        $senderName = SiteSetting::getValue('email_sender_name', config('mail.from.name'));
        $this->from(config('mail.from.address'), $senderName);

        $replyTo = SiteSetting::getValue('email_reply_to');
        if ($replyTo) {
            $this->replyTo($replyTo, $senderName);
        }

        return new Content(
            view: 'emails.contact-reply',
        );
    }
}
