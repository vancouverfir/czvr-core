@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

@include('partials.vendor.datatables')
    <div class="container py-4">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="blue-text fw-bold mt-2">Users</h1>
        @if ($individualCount > 0)
            <p class="text-info">{{ $individualCount === 1 ? '1 user has' : $individualCount.' users have' }} individual permissions! Search for "Individual" to list them.</p>
        @endif

        @if (empty($users))
            <div class="alert alert-danger">No users found</div>
        @else
            <table id="dataTable" class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Rating</th>
                    <th scope="col">Permission</th>
                    <th scope="col">Options</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    @if ($user->deleted == 1)
                    @else
                    <tr>
                        <th scope="row"><b>{{$user->id}}</b></th>
                        <td>{{$user->fullName('FL')}}</td>
                        <td>{{$user->rating_short}}</td>
                        <td>
                            {{ $user->accessLabel() }}
                            @if ($user->direct_permissions_count > 0)
                                <span class="badge text-bg-info rounded shadow-none ms-1">Individual</span>
                            @endif
                        </td>
                        <td>
                            <a class="blue-text" href="{{route('users.viewprofile', $user->id)}}"><i class="fa fa-eye"></i> View User</a>
                        </td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable( {
                "order": [[ 0, "asc" ]]
            } );
        } );
    </script>
@stop
