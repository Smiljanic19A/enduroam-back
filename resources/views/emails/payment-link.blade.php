@extends('emails.layout')

@section('title', 'Payment for Booking #' . $booking->id)

@section('content')
<h2>Payment Request</h2>
<p>Hi {{ $booking->guest_name }},</p>

<div>{!! $customMessage !!}</div>

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

<p style="text-align: center; margin: 24px 0;">
    <a href="{{ $paymentLink }}" class="btn">Pay Now</a>
</p>

<p>If you have any questions, feel free to contact us.</p>
@endsection
