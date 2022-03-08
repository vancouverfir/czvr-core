@extends('layouts.master')
@section('title', 'WPGCore - Winnipeg FIR')
@section('content')
<div class="container d-flex py-5 flex-column align-items-center justify-content-center">
<img src="https://i.imgur.com/tF4WbZu.png" class="img-fluid" style="width: 125px;" alt="">
<h1 class="heading blue-text font-weight-bold display-5">WPGCore</h1>
<h4>Release {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->release}} ({{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_build}})</h4>
<br>
<h5 class="text-center"><a href="https://github.com/winnipegfir/CZWG-core">View our GitHub</a></h5>
<h5 class="text-center"><a href="https://blog.winnipegfir.ca/">Visit the Winnipeg FIR Blog for Updates</a></h5>
<br>
<img src="https://resources.ganderoceanic.com/media/img/brand/sqr/ZQO_SQ_TSPBLUE.png" style="width: 125px">
<br>
<h5 class="text-center"><a href="https://github.com/gander-oceanic-fir-vatsim/czqo-core">Based on the core built by the Gander Oceanic OCA</a></h5>
</div>
@endsection
