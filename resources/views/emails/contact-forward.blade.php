@extends('emails.layout')

@section('title', 'New Contact Message')

@section('content')
<h2>New Contact Message</h2>
<p>A new message has been submitted via the contact form:</p>

<table class="detail-table">
    <tr>
        <td>From</td>
        <td>{{ $contactMessage->name }}</td>
    </tr>
    <tr>
        <td>Email</td>
        <td><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></td>
    </tr>
    <tr>
        <td>Subject</td>
        <td>{{ $contactMessage->subject }}</td>
    </tr>
</table>

<p><strong>Message:</strong></p>
<div class="quote">{{ $contactMessage->message }}</div>

<p>You can reply to this email directly, or manage the message from the admin panel.</p>
@endsection
