@extends('layouts.master')
@section('content')
<div class="container py-4">
    <a href="{{route('network.monitoredpositions.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Monitored Positions</a>
    <h1 class="blue-text font-weight-bold mt-2">{{$position->identifier}}</h1>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h4>Data</h4>
            <form action="">
                <div class="form-group">
                    <label for="">Callsign</label>
                    <input type="text" name="" id="" value="{{$position->identifier}}" class="form-control disabled">
                </div>

                    <div class="pl-0 mt-2 d-flex flex-row justify-content-left">
                        <a href="javascript:editEnable()" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a href="#" class="ml-2 btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>

                </div>
            </form>
        </div>
        <div class="col-md-6">
        </div>
    </div>
    <h4>Sessions</h4>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <thead>
                    <td>Start</td>
                    <td>End</td>
                    <td>Duration</td>
                    <td>CID</td>
                </thead>
                <tbody>
                    @foreach($position->sessions() as $s)
                    <tr>
                        <td>{{$s->session_start}}</td>
                        <td>{{$s->session_end}}</td>
                        <td>{{$s->duration}} hours</td>
                        <td>{{$s->cid}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6">

        </div>
    </div>
</div>
<script>
$('.table').dataTable();

function editEnable() {
    $('input select').removeClass('disabled');
}
</script>
@endsection
