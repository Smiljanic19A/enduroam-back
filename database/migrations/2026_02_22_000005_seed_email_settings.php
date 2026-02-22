<?php

declare(strict_types=1);

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'email_sender_name' => 'Enduroam',
            'email_default_payment_link' => 'https://paypal.me/Enduroam/',
            'email_booking_confirmation' => '<p>Hi {guest_name},</p><p>Thank you for your booking! We will review your booking and get back to you shortly. If you have any questions, feel free to contact us.</p>',
            'email_booking_approved' => '<p>Hi {guest_name},</p><p>Great news! Your booking for <strong>{tour_name}</strong> on <strong>{booking_date}</strong> has been approved. If you have any questions, please don\'t hesitate to contact us.</p>',
            'email_booking_declined' => '<p>Hi {guest_name},</p><p>We\'re sorry to let you know that your booking for <strong>{tour_name}</strong> on <strong>{booking_date}</strong> has been declined. If you have any questions about this, please don\'t hesitate to contact us.</p>',
            'email_booking_cancelled' => '<p>Hi {guest_name},</p><p>Your booking for <strong>{tour_name}</strong> on <strong>{booking_date}</strong> has been cancelled. If you have any questions, please contact us.</p>',
            'email_payment_link' => '<p>Hi {guest_name},</p><p>Please complete your payment for the booking of <strong>{tour_name}</strong>. The deposit amount is <strong>{deposit_amount}</strong>.</p><p>If you have any questions, feel free to contact us.</p>',
            'email_contact_reply' => '<p>Hi {sender_name},</p><p>Thank you for reaching out. We have received your message and here is our response:</p>',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function down(): void
    {
        SiteSetting::whereIn('key', [
            'email_sender_name',
            'email_default_payment_link',
            'email_booking_confirmation',
            'email_booking_approved',
            'email_booking_declined',
            'email_booking_cancelled',
            'email_payment_link',
            'email_contact_reply',
        ])->delete();
    }
};
