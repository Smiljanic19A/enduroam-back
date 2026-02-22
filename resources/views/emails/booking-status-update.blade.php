@extends('emails.layout')

@section('title', 'Booking Status Update')

@section('content')
<h2>Booking Status Update</h2>

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
    <tr>
        <td>Status</td>
        <td><span class="badge badge--{{ $booking->status }}">{{ $booking->status }}</span></td>
    </tr>
</table>
@endsection
