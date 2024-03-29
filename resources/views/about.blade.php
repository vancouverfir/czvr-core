@extends('layouts.master')
@section('title', 'ZVRCore - Vancouver FIR')
@section('content')
<div class="container d-flex py-5 flex-column align-items-center justify-content-center">
<img src="https://cdn.discordapp.com/attachments/800588233570123776/1051930179821391912/Wordmark_Colour.png" class="img-fluid" style="width: 125px;" alt="">
<h1 class="heading blue-text font-weight-bold display-5">CZVRCore</h1>
<h4>Release {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->release}} ({{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_build}})</h4>
<br>
<h5 class="text-center"><a href="https://github.com/vancouverfir/czvr-core">View our GitHub</a></h5>
<br>
<br>
<h5 class="text-center"><a href="https://github.com/winnipegfir/CZWG-core">Based on the website by the Winnipeg FIR</a></h5>
<h5 class="text-center"><a href="https://github.com/gander-oceanic-fir-vatsim/czqo-core">Based on the core built by the Gander Oceanic OCA</a></h5>
</div>
@endsection
