@extends('layouts.master')
@section('title', 'Privacy Policy - Vancouver FIR')
@section('description', 'How the Vancouver FIR collects and uses your data')

@php($privacyPolicyUrl = 'https://czvr.ca/storage/files/uploads/1670724539.pdf')

@push('styles')
<style>
.privacy-frame { overflow: hidden; border: 1px solid var(--czvr-border); border-radius: .5rem; background: var(--czvr-surface); }
.privacy-frame iframe { display: block; width: 100%; height: 80vh; border: 0; }
</style>
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
