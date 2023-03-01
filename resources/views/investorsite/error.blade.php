
@extends('investorsite.navbar')
@section('head')
@section('brand','Error')
    <div class="container custom-container white-box py-2">
@endsection

@section('body')
    <div class="error-code">
        <p>Error {{$status_code}}</p>
    </div>
    <div class="error-msg text-left" role="alert">
        <p class="text-left"><b>Sorry, it seems something has gone wrong on our server.</b></p><br>
        {{$err_msg}}
    </div>
@endsection