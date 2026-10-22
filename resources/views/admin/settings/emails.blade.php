@extends('layouts.master')
@push('styles')<link href="{{ asset('css/pages/admin-settings.css') }}" rel="stylesheet">@endpush
@section('content')
<main class="container py-4 settings-hub settings-editor">
    <a href="{{ route('settings.index') }}" class="settings-back"><i class="fas fa-arrow-left"></i> Settings</a>
    <div class="settings-heading"><div><h1 class="blue-text">Staff Emails</h1><p>Addresses used for contact links and automated website messages.</p></div></div>
    <form method="POST" action="{{ route('settings.emails.post') }}" class="settings-form">@csrf
        @foreach(['emailfirchief' => 'FIR Chief', 'emaildepfirchief' => 'Deputy FIR Chief', 'emailcinstructor' => 'Chief Instructor', 'emaileventc' => 'Events Coordinator', 'emailfacilitye' => 'Facility Engineer', 'emailwebmaster' => 'Webmaster'] as $field => $label)
            <div class="settings-field"><label for="{{ $field }}">{{ $label }}</label><div><input id="{{ $field }}" required type="email" value="{{ old($field, $coreSettings->{$field}) }}" name="{{ $field }}" class="form-control"></div></div>
        @endforeach
        <div class="settings-actions"><button type="submit" class="btn btn-success">Save changes</button></div>
    </form>
</main>
@endsection
