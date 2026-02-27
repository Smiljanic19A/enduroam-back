@extends('emails.layout')

@section('title', 'Payment for Booking #' . $booking->id)

@section('content')
<h2>Payment Request</h2>

<div>{!! $body !!}</div>

<table class="detail-table">
    <tr>
        <td>Booking #</td>
        <td>{{ $booking->id }}</td>
    </tr>
    <tr>
        <td>{{ class_basename($booking->bookable_type) }}</td>
        <td>{{ $booking->bookable->name }}</td>
    </tr>
    <tr>
        <td>Date</td>
        <td>{{ $booking->start_date->format('l, F j, Y') }}</td>
    </tr>
    <tr>
        <td>Guests</td>
        <td>{{ $booking->number_of_guests }}</td>
    </tr>
    <tr>
        <td>Total</td>
        <td>{{ $booking->currency }}{{ number_format((float) $booking->total_price, 2) }}</td>
    </tr>
    @if($booking->deposit_amount && (float) $booking->deposit_amount < (float) $booking->total_price)
    <tr>
        <td>Deposit Due</td>
        <td style="font-weight: 700;">{{ $booking->currency }}{{ number_format((float) $booking->deposit_amount, 2) }}</td>
    </tr>
    @endif
</table>

{{-- Payment Options --}}
<div style="margin: 32px 0 16px; text-align: center;">
    <p style="font-size: 16px; font-weight: 700; color: #1a1a2e; margin: 0 0 24px;">Choose your payment method</p>

    {{-- Option 1: PayPal --}}
    <div style="margin-bottom: 28px;">
        <p style="font-size: 13px; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Option 1 — PayPal</p>
        <a href="{{ $paymentLink }}" class="btn" style="display: inline-block;">Pay with PayPal</a>
    </div>

    {{-- Divider --}}
    <div style="margin: 24px auto; max-width: 200px; border-top: 1px solid #e0e0e0;"></div>

    {{-- Option 2: IPS QR --}}
    <div>
        <p style="font-size: 13px; color: #888888; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px;">Option 2 — IPS Bank Transfer</p>
        <p style="font-size: 14px; color: #333333; margin: 0 0 16px;">Scan the QR code below with your banking app</p>
        <img src="{{ $ipsQrUrl }}" alt="IPS QR Code" width="250" height="250" style="display: block; margin: 0 auto; border: 1px solid #f0f0f0;" />
        @if($booking->deposit_amount && (float) $booking->deposit_amount < (float) $booking->total_price)
        <p style="font-size: 13px; color: #666666; margin: 12px 0 0;"><strong>Pay deposit amount only: {{ $booking->currency }}{{ number_format((float) $booking->deposit_amount, 2) }}</strong></p>
        @endif
    </div>
</div>
@endsection
