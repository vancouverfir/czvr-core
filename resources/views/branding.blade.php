@extends('layouts.master')
@section('title', 'Branding - Vancouver FIR')
@section('description', 'Vancouver FIR Branding')
@section('content')
<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Branding</h1>
    We kindly request that you maintain the images in their original, published state without making modifications. Resizing is acceptable, as long as the aspect ratio remains unaltered.
    <hr>
    <h4 class="font-weight-bold blue-text">Horizontal Logos</h4>
    <div class="pt-1">
        <div class="row">
            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <img src="https://czvr.ca/storage/files/branding/czvr-long-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-long-wordmark.png">Color Long Wordmark</a>
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <img style="background-color: #fffff" src="https://czvr.ca/storage/files/branding/czvr-short-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-short-wordmark.png">Color Short Wordmark</a>
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center" style="padding-top: 2%">
                <img style="background-color: #ffffff" src="https://czvr.ca/storage/files/branding/czvr-bw-inv-long-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-bw-inv-long-wordmark.png">Inverted Gray Long Wordmark</a>
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center" style="padding-top: 2%">
                <img style="background-color: #fffff" src="https://czvr.ca/storage/files/branding/czvr-bw-inv-short-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-bw-inv-short-wordmark.png">Inverted Gray Short Wordmark</a>
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center" style="padding-top: 2%">
                <img style="background-color: #fffff" src="https://czvr.ca/storage/files/branding/czvr-bw-long-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-bw-long-wordmark.png">Gray Long Wordmark</a>
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center" style="padding-top: 2%">
                <img style="background-color: #fffff" src="https://czvr.ca/storage/files/branding/czvr-bw-short-wordmark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-bw-short-wordmark.png">Gray Short Wordmark</a>
            </div>
        </div>
</div>

<h4 class="font-weight-bold blue-text">Square Logos</h4>
    <div class="pt-1">
        <div class="row">
            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <img style="background-color: #fffff; height: 10rem;" src="https://czvr.ca/storage/files/branding/czvr-logomark.png" class="img-fluid" alt="">
                <br/>
                <a href="hhttps://czvr.ca/storage/files/branding/czvr-logomark.png">Color Logo</a>
            </div>
        
        <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
            <img style="background-color: #ffffff; height: 10rem;" src="https://czvr.ca/storage/files/branding/czvr-logo-square.png" class="img-fluid" alt="">
            <br/>
            <a href="https://czvr.ca/storage/files/branding/czvr-logo-square.png">Color Logo Square</a>
        </div>

        <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <img style="height: 10rem;" src="https://czvr.ca/storage/files/branding/czvr-bw-logomark.png" class="img-fluid" alt="">
                <br/>
                <a href="https://czvr.ca/storage/files/branding/czvr-bw-logomark.png">Gray Logo</a>
        </div>
</div>

<style>
    .color-palette {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }
    .color-box {
        width: 120px;
        height: 120px;
        border-radius: 5px;
        margin: 0 3px 5px 3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        color: #fff;
    }
    .color-box-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 1px 1px 1px;
    }
</style>

<div class="container py-4">
    <hr>
    <h4 class="font-weight-bold blue-text">CZVR Colors</h4>
    <div class="color-palette">
    	<div class="color-box-container">
        <div class="color-box" style="background-color: #6cc24a;"></div>
        <div class="color-name">#6cc24a</div>
        </div>
        <div class="color-box-container">
        <div class="color-box" style="background-color: #817f7c;"></div>
        <div class="color-name">#817f7c</div>
        </div>
        <div class="color-box-container">
        <div class="color-box" style="background-color: #cbcbc8;"></div>
        <div class="color-name">#cbcbc8</div>
        </div>
    </div>
    
</div>

@endsection
