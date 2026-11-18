@extends('layouts.master')
@section('content')
    <div class="container py-4">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text fw-bold mt-2">Audit Log</h1>
        <hr>
        <form class="d-flex gap-2 mb-3" method="GET">
            <input type="search" name="q" value="{{ request('q') }}" maxlength="100" class="form-control" aria-label="Action or CID" placeholder="Search by action or CID">
            <button class="btn btn-primary">Search</button>
        </form>
        @if (count($entries) < 1)
            <p>No logs</p>
        @else
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Time</th>
                        <th scope="col">User</th>
                        <th scope="col">Affected User</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <th scope="row">{{$entry->created_at}}</th>
                        <td>
                            {{ $entry->user?->fullName('FLC') ?? $entry->user_id }}
                        </td>
                        <td>
                            @if((int) $entry->affected_id === 1)
                                NO ONE AFFECTED
                            @else
                            {{ $entry->affectedUser?->fullName('FLC') ?? $entry->affected_id }}
                            @endif
                        </td>
                        <td>
                            {{$entry->action}}
                            &nbsp;
                            @if ($entry->private == 1)
                            (private)
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        {{ $entries->links() }}
    </div>
@endsection
