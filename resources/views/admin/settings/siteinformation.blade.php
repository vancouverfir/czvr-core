@extends('layouts.master')
@push('styles')<link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">@endpush
@section('content')
<main class="container py-4 settings-hub settings-editor">
    <a href="{{ route('settings.index') }}" class="settings-back"><i class="fas fa-arrow-left"></i> Settings</a>
    <div class="settings-heading"><div><h1 class="blue-text">Site Information</h1><p>Core identity and release details shown across the website.</p></div></div>
    <form method="POST" action="{{ route('settings.siteinformation.post') }}" class="settings-form">@csrf
        <div class="settings-field"><label for="sys_name">System name</label><div><input id="sys_name" required type="text" value="{{ old('sys_name', $coreSettings->sys_name) }}" name="sys_name" class="form-control"><small>The public name used for Core.</small></div></div>
        <div class="settings-field"><label for="release">Version number</label><div><input id="release" required type="text" value="{{ old('release', $coreSettings->release) }}" name="release" class="form-control"></div></div>
        <div class="settings-field"><label for="sys_build">Build date</label><div><input id="sys_build" required type="text" value="{{ old('sys_build', $coreSettings->sys_build) }}" name="sys_build" class="form-control"></div></div>
        <div class="settings-field"><label for="copyright_year">Copyright year</label><div><input id="copyright_year" required type="number" value="{{ old('copyright_year', $coreSettings->copyright_year) }}" name="copyright_year" class="form-control"></div></div>
        <div class="settings-actions"><button type="submit" class="btn btn-success">Save changes</button></div>
    </form>
</main>
@endsection
