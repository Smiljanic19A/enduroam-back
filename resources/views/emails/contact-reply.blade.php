@extends('emails.layout')

@section('title', 'Re: ' . $contactMessage->subject)

@section('content')
<h2>Re: {{ $contactMessage->subject }}</h2>
<p>Hi {{ $contactMessage->name }},</p>

<div>{!! $body !!}</div>

<p style="margin-top: 24px; font-size: 13px; color: #888888;">--- Original Message ---</p>
<div class="quote">{{ $contactMessage->message }}</div>
@endsection
