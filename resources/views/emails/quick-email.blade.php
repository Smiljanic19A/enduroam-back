@extends('emails.layout')

@section('title', $emailSubject)

@section('content')
<div>{!! $body !!}</div>
@endsection
