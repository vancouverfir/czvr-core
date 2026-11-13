@extends('layouts.master')
@section('title', 'Privacy Policy - Vancouver FIR')
@section('description', 'How the Vancouver FIR collects and uses your data')

@php($privacyPolicyUrl = 'https://czvr.ca/storage/files/uploads/1670724539.pdf')

@push('styles')
    <link href="{{ asset('css/pages/public.css') }}" rel="stylesheet">
@endpush

@section('content')
<main class="container py-4">
    <div class="page-heading">
        <h1 class="blue-text">Privacy Policy</h1>
    </div>

    <div class="privacy-frame">
        <iframe src="{{ $privacyPolicyUrl }}" title="Vancouver FIR Privacy Policy"></iframe>
    </div>
    <p class="small text-muted mt-2 mb-0">If the PDF is not displaying correctly, you can <a href="{{ $privacyPolicyUrl }}" target="_blank" rel="noopener noreferrer">open it directly here</a>!</p>
</main>
@endsection
