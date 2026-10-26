@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Policies')
@section('description', 'Policies and Guidelines from the Vancouver FIR')

@section('content')
@include('partials.vendor.flatpickr')
    <div class="container" style="margin-top: 20px;">
        <h1 class="fw-bold blue-text">Policies</h1>

        @can('policies.manage')
            <hr>
            <div class="card" style="max-width: 720px;">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Policy Admin</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addPolicyModal" class="btn btn-sm btn-primary">Add New Policy</a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addPolicySectionModal" class="btn btn-sm btn-light">Add New Policy Section</a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#deletePolicySectionModal" class="btn btn-sm btn-danger">Delete Policy Sections</a>
                    </div>
                </div>
            </div>
        @endcan
        @foreach($policySections as $s)
            <hr>
            <h5 class="fw-bold">{{$s->section_name}}</h5>
            @foreach ($s->policies as $policy)
                <div id="policyAccordion{{$policy->id}}">
                    <div aria-expanded="true"  class="card">
                        <div class="card-header" id="policyHeading{{$policy->id}}">
                            <h5 class="mb-0">
                                <button class="text-colour btn btn-link" data-bs-toggle="collapse" data-bs-target="#policy{{$policy->id}}" aria-expanded="true" aria-controls="policy{{$policy->id}}">
                                    {{ $policy->name }}
                                    @if($policy->staff_only == 1)
                                        - <b class="text-danger">This is a private staff-only policy.</b>
                                    @endif
                                </button>
                            </h5>
                        </div>
                        <div id="policy{{$policy->id}}" class="collapse" aria-labelledby="policyHeading{{$policy->id}}" data-bs-parent="#policyAccordion{{$policy->id}}">
                            <div class="card-body">
                                @can('policies.manage')
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editPolicyModal{{$policy->id}}" class="btn btn-sm btn-primary">Edit Policy</a>
                                        <form method="POST" action="{{url('/policies/'.$policy->id.'/delete')}}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete Policy</button>
                                        </form>
                                    </div>
                                @endcan
                                <b>Effective Date: {{$policy->releaseDate }}</b>
                                <p>{{$policy->details}}</p>
                                <a target="_blank" href="{{$policy->link}}">Download the .PDF file HERE.</a>
                                @if ($policy->embed == 1)
                                    <iframe width="100%" style="height: 600px; border: none;" src="{{$policy->link}}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            @endforeach
            <br>
        @endforeach
        @auth
            @can('policies.manage')
                <hr>
                <h5 class="fw-bold text-danger" >Policies without a section, staff only view</h5>
                @if(count($nullPolicies) >= 1)
                    @foreach($nullPolicies as $policy)
                        <div id="policyAccordion{{$policy->id}}">
                            <div aria-expanded="true"  class="card">
                                <div class="card-header" id="policyHeading{{$policy->id}}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#policy{{$policy->id}}" aria-expanded="true" aria-controls="policy{{$policy->id}}">
                                            {{ $policy->name }}
                                            @if($policy->staff_only == 1)
                                                - <b class="text-danger">This is a private staff-only policy.</b>
                                            @endif
                                        </button>
                                    </h5>
                                </div>
                                <div id="policy{{$policy->id}}" class="collapse" aria-labelledby="policyHeading{{$policy->id}}" data-bs-parent="#policyAccordion{{$policy->id}}">
                                    <div class="card-body">
                                            <div class="d-flex flex-wrap gap-2 border p-2 mb-3">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPolicyModal{{$policy->id}}" class="btn btn-sm btn-primary">Edit Policy</a>
                                                <form method="POST" action="{{url('/policies/'.$policy->id.'/delete')}}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete Policy</button>
                                                </form>
                                            </div>
                                        <b>Effective Date: {{$policy->releaseDate }}</b>
                                        <p>{{$policy->details}}</p>
                                        <a target="_blank" href="{{$policy->link}}">Download the .PDF file HERE.</a>
                                        @if ($policy->embed == 1)
                                            <iframe width="100%" style="height: 600px; border: none;" src="{{$policy->link}}"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    @endforeach
                @else
                    <h6 class="fw-bold">🎉 Congratulations! 🎉 There are no policies without a section.</h6>
                    <br>
                @endif
            @endcan
        @endauth
        <br>


        <div class="modal fade" id="addPolicyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{route('policies.create')}}" enctype="multipart/form-data">
                        @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New Policy</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Policy Name</label>
                            <input type="text" name="name" class="form-control"></input>
                          </div>
                          <div class="form-group">
                              <label for="recipient-name" class="col-form-label">Policy Section</label>
                              <select name="section" class="form-control">
                                  <option value="-1" hidden>Select a policy section...</option>
                                  @foreach($policySections as $p)
                                      <option value="{{$p->id}}">{{$p->id}} - {{$p->section_name}}</option>
                                  @endforeach
                              </select>
                          </div>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Details (Max 250 Char.)</label>
                            <textarea name="details" class="form-control"></textarea>
                          </div>
                          <div class="form-group">
                              <label class="col-form-label">Policy File (PDF)</label>
                              <input type="file" name="file" class="form-control" accept="application/pdf" required>
                          </div>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Display Options</label>
                            <select name="embed" class="form-control">
                                <option value="1" selected>Display</option>
                                <option value="0">Do Not Display</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Privacy</label>
                            <select name="staff_only" class="form-control">
                              <option value="0">Public</option>
                              <option value="1">Private to staff only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Publishing Notification</label>
                            <select name="email" class="form-control">
                              <option value="none">Do Nothing (Default)</option>
                              <option value="all">Email ALL Users & Publish News Article</option>
                              <option value="emailcert">Email Certified Controllers and Publish as News Article</option>
                              <option value="newsonly">Only Publish as News Article</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Effective Date</label>
                            <input type="datetime" name="date" placeholder="Choose a Date" class="form-control flatpickr policy-date"></input>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @can('policies.manage')
        @foreach($allPolicies as $p)
            <div class="modal fade" id="editPolicyModal{{$p->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{url('/policies/'.$p->id.'/edit')}}" enctype="multipart/form-data">
                            @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Policy - {{$p->name}}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Policy Name</label>
                                    <input type="text" name="name" class="form-control" value="{{$p->name}}"></input>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Policy Section</label>
                                    <select name="section" class="form-control">
                                        <option value="-1" hidden>Select a policy section...</option>
                                        @foreach($policySections as $ps)
                                            <option value="{{$ps->id}}" {{$ps->id == $p->section_id ? "selected=selected" : ""}}>{{$ps->id}} - {{$ps->section_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Details (Max 250 Char.)</label>
                                    <textarea name="details" class="form-control">{{$p->details}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">
                                        Replace Policy File (PDF)
                                    </label>

                                    <input type="file" name="file" class="form-control" accept="application/pdf">

                                    <small class="form-text text-muted">
                                        Leave empty to keep the current file.
                                        <br>
                                        Current:
                                        <a href="{{ $p->link }}" target="_blank">View existing file</a>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Display Options</label>
                                    <select name="embed" class="form-control">
                                        <option value="1" {{$p->embed == 1 ? "selected=selected" : ""}}>Display</option>
                                        <option value="0" {{$p->embed == 0 ? "selected=selected" : ""}}>Do Not Display</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Privacy</label>
                                    <select name="staff_only" class="form-control">
                                        <option value="0" {{$p->staff_only == 0 ? "selected=selected" : ""}}>Public</option>
                                        <option value="1" {{$p->staff_only == 1 ? "selected=selected" : ""}}>Private to staff only</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Effective Date</label>
                                    <input type="datetime" name="date" placeholder="Choose a Date" class="form-control flatpickr policy-date"></input>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan

    <div class="modal fade" id="addPolicySectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{route('policysection.create')}}">
                    @CSRF
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Create Policy Section
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Policy Section Name</label>
                            <input type="text" name="name" class="form-control"></input>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePolicySectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Delete Policy Sections
                    </h5>
                </div>
                <div class="modal-body">
                    @foreach($policySections as $s)
                        <div class="form-group">
                            <text class="fw-bold">{{$s->id}} - {{$s->section_name}}</text>
                            <form method="POST" action="{{url('/policies/section/'.$s->id.'/delete/')}}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete Section</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn light" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('.policy-date', {
                enableTime: false,
                noCalendar: false,
                dateFormat: 'Y-m-d',
                time_24hr: true,
            });
        }
    });
</script>
@endpush
