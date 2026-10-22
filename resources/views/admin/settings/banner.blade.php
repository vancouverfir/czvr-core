@extends('layouts.master')
@push('styles')<link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">@endpush
@section('content')
<main class="container py-4 settings-hub settings-editor">
    <a href="{{ route('settings.index') }}" class="settings-back"><i class="fas fa-arrow-left"></i> Settings</a>
    <div class="settings-heading"><div><h1 class="blue-text">Site Banner</h1><p>Publish a short notice above the navigation, or choose Hidden to remove it.</p></div></div>
    <form method="POST" action="{{ route('settings.banner.edit') }}" class="settings-form">@csrf
        <div class="settings-field"><label for="bannerModeSelect">Appearance</label><div><select name="bannerMode" id="bannerModeSelect" class="form-select"><option value="hidden" @selected(old('bannerMode', $banner->bannerMode ?: 'hidden') === 'hidden')>Hidden</option>@foreach(['success' => 'Success', 'danger' => 'Danger', 'warning' => 'Warning', 'info' => 'Info', 'primary' => 'Primary', 'secondary' => 'Secondary', 'light' => 'Light', 'dark' => 'Dark', 'muted' => 'Muted', 'white' => 'White'] as $value => $label)<option value="{{ $value }}" data-color="{{ $value }}" @selected(old('bannerMode', $banner->bannerMode) === $value)>{{ $label }}</option>@endforeach</select><small>The dropdown reflects the color visitors will see.</small></div></div>
        <div class="settings-field"><label for="bannerMessage">Message</label><div><input id="bannerMessage" name="bannerMessage" class="form-control" value="{{ old('bannerMessage', $banner->banner) }}" placeholder="Scheduled maintenance begins at 20:00 UTC."></div></div>
        <div class="settings-field"><label for="bannerLink">Link <span>optional</span></label><div><input id="bannerLink" name="bannerLink" type="url" class="form-control" value="{{ old('bannerLink', $banner->bannerLink) }}" placeholder="https://czvr.ca/news"></div></div>
        <div class="settings-actions"><button class="btn btn-success">Save banner</button></div>
    </form>
</main>
@endsection
