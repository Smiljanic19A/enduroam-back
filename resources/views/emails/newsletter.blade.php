@extends('emails.layout')

@section('title', $subject)

@section('content')
<h2>{{ $subject }}</h2>

<div>{!! $body !!}</div>

<p style="margin-top: 32px; text-align: center; font-size: 13px; color: #888888;">
    <a href="{{ $unsubscribeUrl }}" style="color: #888888;">Unsubscribe from this newsletter</a>
</p>
@endsection
