@extends('layouts.master')
@section('title', 'You found Mochi!')
@section('content')

<div class="container py-4">
    <h1><strong>Congrats! You've found an Easter Egg!</strong></h1>
        <p>We don't really have anything to give you as a gift, but please feel free to click on Mochi below this text for a free chirping!</p>
    <hr>
        <img src="https://czvr.ca/storage/files/easteregg/1671213201.png" style="width: 100%; height: auto;" onclick="clickMochi()">
    <br></br>
        <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Take Me Home (Country Roads)</a>
</div>

<script>
function clickMochi() {
  let txt;
  if (confirm("Chirp!")) {
  }
  document.getElementById("mochi").innerHTML = txt;
}
</script>

@endsection
