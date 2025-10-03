@extends('layouts.master')
@section('title', 'VFR - Vancouver FIR')
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />

    <div class="container py-4">
        <h1 class="font-weight-bold blue-text"><strong>VFR in CZVR</strong></h1>
        <p class="text-colour content-paragraph">CZVR is home to some of the most beautiful landscapes to navigate visually. 
        With increasing flight sim fidelity and distinct landscapes, CZVR provides easy visual navigation 
        and world class sight seeing.  Both new and seasoned VFR pilots are welcome. No need to be intimidated, 
        our friendly controllers  are happy to help learn when possible. </p>
        <hr>
        <h2 class="font-weight-bold blue-text">VFR Charts and Procedures</h2>
        <p class="text-colour content-paragraph">Within the FIR we have been implementing the use of real world VFR procedures. 
        Procedures may be assigned based on destination and traffic. The most up to date of these can be found here:</p>
            <h5 class="text-colour content-link"><a href="https://imageserver.fltplan.com/legends/VTA_BACK_VANCOUVER.PDF" class="text-colour"><text>VTA Back Vancouver</text></a></h5>
            <h5 class="text-colour content-link"><a href="https://imageserver.fltplan.com/legends/BC_VFR_TERMINAL_PROCEDURES_08-07-2025.PDF" class="text-colour"><text>Canadian Flight Supplement - BC VFR Terminal Procedures</text></a></h5>
        <hr>
        <p class ="content-warning">It is likely these procedures may be new to many Vatsim pilots.  
        Although the procedures may be enforced it is important not to accept a procedure if you do not understand it.
         If you do not understand ask the controller for assistance or clarity as needed, we are here to help and learn together. 
         For example, as controller loads permit we may instead assign a heading or altitude to emulate the procedure. </p>
        <hr>
        <h2 class="font-weight-bold blue-text">FSS for Pilots</h2>
        <h5 class="text-colour content-link"><a href="https://czvr.ca/storage/files/fss-handbook.pdf" class="text-colour"><text>Flight Who? - Introduction to FSS for Pilots</text></a></h5>
        <hr>
        <h2 class="font-weight-bold blue-text">Inner/Outer Tower</h2>
        <p class="text-colour content-paragraph">Many of the following procedures indicated points to report/frequency change from inner to outer tower. 
        In the case both inner and outer tower are online no modifications are needed. However, if only inner tower is online this 
        controller will inherit the responsibilities of outer tower. In this case, simply follow the procedure with out frequency 
        change and reporting as outlined to inner tower. Inner tower will always be the standard tower callsign
         (ex: CYVR_TWR, CYYJ_TWR, CYVR_1_TWR, etc.) </p>  
    </div>

@endsection
