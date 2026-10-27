@extends('layouts.master')

@section('title', 'Submit Feedback - Vancouver FIR')
@section('description', 'Submit feedback for our controllers')

@push('styles')
    <link href="{{ asset('css/pages/feedback.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container py-4">
        <h1 class="blue-text mb-1">Submit feedback</h1>
        <p class="text-muted mb-4">Share feedback about a controller, event, or the website!</p>

        <div class="feedback-form-shell">
            <div class="feedback-form-heading">
                <h5>Feedback details</h5>
            </div>

            <div class="feedback-form-body">
                @if ($errors->createFeedbackErrors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->createFeedbackErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('feedback.create.post') }}" method="POST">
                    @csrf

                    <label for="feedbackTypeSelect" class="form-label">Feedback type</label>
                    <select name="feedbackType" id="feedbackTypeSelect" class="form-select" required>
                        <option value="">Select a type</option>
                        <option value="controller" @selected(old('feedbackType') === 'controller')>Controller feedback</option>
                        <option value="event" @selected(old('feedbackType') === 'event')>Event feedback</option>
                        <option value="website" @selected(old('feedbackType') === 'website')>Website feedback</option>
                    </select>

                    <div id="feedbackPrompt" class="alert alert-light border mt-3 mb-0">
                        Select a feedback type to continue!
                    </div>

                    <div id="feedbackFields" class="feedback-fields" hidden>
                        <div class="mb-3" data-feedback-for="controller" hidden>
                            <label for="controllerCid" class="form-label">Controller</label>
                            <select name="controllerCid" id="controllerCid" class="form-select" required data-searchable>
                                <option value="">Select a controller</option>
                                @foreach ($controllers as $controller)
                                    <option value="{{ $controller->cid }}" @selected((string) old('controllerCid') === (string) $controller->cid)>
                                        {{ $controller->cid }} — {{ $controller->user->fullName('FL') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" data-feedback-for="controller" hidden>
                            <label for="feedbackPosition" class="form-label">Position</label>
                            <input type="text" name="position" id="feedbackPosition" class="form-control"
                                value="{{ old('position') }}" placeholder="CZVR_CTR" maxlength="100" required>
                        </div>

                        <div class="mb-3" data-feedback-for="event website" hidden>
                            <label for="feedbackSubject" class="form-label">Subject</label>
                            <input type="text" name="subject" id="feedbackSubject" class="form-control"
                                value="{{ old('subject') }}" maxlength="255" required>
                        </div>

                        <div class="mb-3">
                            <label for="feedbackContent" class="form-label">Your feedback</label>
                            <textarea name="content" id="feedbackContent" class="form-control" rows="7"
                                maxlength="20000" required>{{ old('content') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>Submit feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            const $type = $('#feedbackTypeSelect');

            function updateForm() {
                const type = $type.val();

                $('#feedbackPrompt').prop('hidden', type !== '');
                $('#feedbackFields').prop('hidden', type === '');

                $('[data-feedback-for]').each(function () {
                    const shown = this.dataset.feedbackFor.split(' ').includes(type);

                    this.hidden = !shown;
                    $(this).find('input, select').prop('disabled', !shown);
                });

                $('#feedbackSubject').attr('placeholder', type === 'event' ? 'Cross the Pond 2025, FNO, West Coast Weekends...' : '');
            }

            $type.on('change', updateForm);
            updateForm();
        });
    </script>
@endsection
