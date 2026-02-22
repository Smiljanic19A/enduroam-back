@extends('emails.layout')

@section('title', 'Booking Status Update')

@section('content')
<h2>Booking Status Update</h2>
<p>Hi {{ $booking->guest_name }},</p>
<p>Your booking status has been updated to: <span class="badge badge--{{ $booking->status }}">{{ $booking->status }}</span></p>

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
</table>

<p>If you have any questions about this update, please don't hesitate to contact us.</p>
@endsection
