@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Edit User')
@section('description', "Vancouver FIR's Controller Roster")

@section('content')

<div class="container" style="margin-top: 20px;">
    <a href="{{route('roster.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Roster</a>
<br>
<head>
<style>
* {
  box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 50%;
  padding: 10px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
</head>

<div align="center">
<form method="post" action="{{route('roster.editcontroller', [$cid]) }}"<br>
  <form class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Edit Controller on Roster</legend>


<div class="form-group">
  <label>Controller CID:</label><br>
  {{$roster->full_name." ".$cid}}<br><br>

  <!--FSS-->

    <input type="hidden" name="cid" value="{{ $cid }}">
</div>
<div class="form-row">
  <div class="col-md-3">
  </div>
<div class="form-group col-md-2">
  <div align="center">
  <label class="control-label" for="fss">FSS</label>
  <div align="center">
  <label class="control-label" for="fss">Unrestricted</label>
    <select name="fss" class="form-control">
      <option value="0"{{ $roster->fss == "0" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="1"{{ $roster->fss == "1" ? "selected=selected" : ""}}>Mentor</option>
      <option value="2"{{ $roster->fss == "2" ? "selected=selected" : ""}}>Solo</option>
      <option value="3"{{ $roster->fss == "3" ? "selected=selected" : ""}}>Certified</option>
    </select>
  </div>
</div>
  </div>

<!-- Delivery/Ground -->

<div class="form-group col-md-2">
  <label class="control-label" for="delgnd">Delivery/Ground</label>
  <div align="center">
  <label class="control-label" for="delgnd">Unrestricted</label>
  <select name="delgnd" class="form-control">
    <option value="0"{{ $roster->delgnd == "0" ? "selected=selected" : ""}}>Not Certified</option>twr
    <option value="1"{{ $roster->delgnd == "1" ? "selected=selected" : ""}}>Mentor</option>
    <option value="2"{{ $roster->delgnd == "2" ? "selected=selected" : ""}}>Solo</option>
    <option value="3"{{ $roster->delgnd == "3" ? "selected=selected" : ""}}>Certified</option>
  </select>
  <br>
  <label class="control-label" for="delgnd">Tier 2</label>
  <select name="delgnd_t2" class="form-control">
    <option value="0"{{ $roster->delgnd_t2 == "0" ? "selected=selected" : ""}}>Not Certified</option>
    <option value="1"{{ $roster->delgnd_t2 == "1" ? "selected=selected" : ""}}>Mentor</option>
    <option value="2"{{ $roster->delgnd_t2 == "2" ? "selected=selected" : ""}}>Solo</option>
    <option value="3"{{ $roster->delgnd_t2 == "3" ? "selected=selected" : ""}}>Certified</option>
  </select>
  </div>
</div>


<!-- Tower -->

<div class="form-group col-md-2">
  <label class="control-label" for="twr">Tower</label>
  <div align="center">
  <label class="control-label" for="twr">Unrestricted</label>
    <select name="twr" class="form-control">
      <option value="0"{{ $roster->twr == "0" ? "selected=selected" : ""}}>Not Certified</option>twr
      <option value="1"{{ $roster->twr == "1" ? "selected=selected" : ""}}>Mentor</option>
      <option value="2"{{ $roster->twr == "2" ? "selected=selected" : ""}}>Solo</option>
      <option value="3"{{ $roster->twr == "3" ? "selected=selected" : ""}}>Certified</option>
    </select>
    <br>
    <label class="control-label" for="twr">Tier 2</label>
    <select name="twr_t2" class="form-control">
      <option value="0"{{ $roster->twr_t2 == "0" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="1"{{ $roster->twr_t2 == "1" ? "selected=selected" : ""}}>Mentor</option>
      <option value="2"{{ $roster->twr_t2 == "2" ? "selected=selected" : ""}}>Solo</option>
      <option value="3"{{ $roster->twr_t2 == "3" ? "selected=selected" : ""}}>Certified</option>
    </select>
  </div>

</div>
</div>

<br><br>


<!-- Departure -->

<div class="form-row">
  <div class="col-md-3">
  </div>
<div class="form-group col-md-2">
  <label class="control-label" for="dep">Departure</label>
  <div align="center">
      <label class="control-label" for="dep">Tier 2</label>
      <select name="dep" class="form-control">
        <option value="0"{{ $roster->dep == "0" ? "selected=selected" : ""}}>Not Certified</option>
        <option value="1"{{ $roster->dep == "1" ? "selected=selected" : ""}}>Mentor</option>
        <option value="2"{{ $roster->dep == "2" ? "selected=selected" : ""}}>Solo</option>
        <option value="3"{{ $roster->dep == "3" ? "selected=selected" : ""}}>Certified</option>
      </select>
    </div>
  </div>

<br><br>


<!-- Approach -->

<div class="form-group col-md-2">
  <label class="control-label" for="app">Arrival</label>
  <div align="center">
    <label class="control-label" for="app">Unrestricted</label>
      <select name="app" class="form-control">
        <option value="0"{{ $roster->app == "0" ? "selected=selected" : ""}}>Not Certified</option>
        <option value="1"{{ $roster->app == "1" ? "selected=selected" : ""}}>Mentor</option>
        <option value="2"{{ $roster->app == "2" ? "selected=selected" : ""}}>Solo</option>
        <option value="3"{{ $roster->app == "3" ? "selected=selected" : ""}}>Certified</option>
      </select>
      <br>
      <label class="control-label" for="app">Tier 2</label>
      <select name="app_t2" class="form-control">
        <option value="0"{{ $roster->app_t2 == "0" ? "selected=selected" : ""}}>Not Certified</option>
        <option value="1"{{ $roster->app_t2 == "1" ? "selected=selected" : ""}}>Mentor</option>
        <option value="2"{{ $roster->app_t2 == "2" ? "selected=selected" : ""}}>Solo</option>
        <option value="3"{{ $roster->app_t2 == "3" ? "selected=selected" : ""}}>Certified</option>
      </select>
    </div>
  </div>

<br><br>
<!-- Center -->
<div class="form-group col-md-2">
  <label class="control-label" for="ctr">Centre</label>
  <div align="center">
    <label class="control-label" for="ctr">Tier 2</label>
    <select name="ctr" class="form-control">
      <option value="0"{{ $roster->ctr == "0" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="1"{{ $roster->ctr == "1" ? "selected=selected" : ""}}>Training</option>
      <option value="2"{{ $roster->ctr == "2" ? "selected=selected" : ""}}>Solo</option>
      <option value="3"{{ $roster->ctr == "3" ? "selected=selected" : ""}}>Certified</option>
    </select>
  </div>
  </div>
</div>
<br>

<!--Remarks-->
<div class="form-group">
  <label class="control-label" for="remarks">Remarks</label><br>
  <textarea name="remarks" rows="1" cols="5" class="form-control">{{ $roster->remarks }}
  </textarea>
</div>



  <!--Active Status-->
    <div class="form-row">
        <div class="col-md-4">
        </div>
        <div class="form-group col-md-2">
            <label class="control-label" for"active">Active</label><br>
            <select name="active" class="form-control" style="width:75px">
                <option value="1"{{ $roster->active == "1" ? "selected=selected" : ""}}>Active</option>
                <option value="0"{{ $roster->active == "0" ? "selected=selected" : ""}}>Not Active</option>
            </select>
        </div>
        <!-- Rating Hours-->
        <div class="form-group col-md-2">
            <label class="control-label" for "rating_hours">Reset rating hours?</label><br>
            <select style="width:75px" name="rating_hours" class="form-control">
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
        </div>
    </div>
@csrf
<!-- Button -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="submit"></label>
        <div class="col-md-4">
            <button name="submit" class="btn btn-success">Submit</button>
        </div>
    </div>
    </fieldset>
  </form>
</div>

@stop
