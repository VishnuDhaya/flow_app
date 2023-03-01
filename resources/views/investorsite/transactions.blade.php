@php
$currency = get_currency_sign($bonds[0]->currency_code);
$realisation_arr = ['investment' => "Start/Allocation date of the bond", 'redemption' => "End date of bond", 'payout' => "Date of coupon accrual"];
$txns_arr = ['investment' => "Date investment was received by Flow", 'redemption' => "Date redemption was initiated by Flow", 'payout' => "Date payout was released by Flow"];

@endphp


@section('head')
@extends('investorsite.navbar')
@section('brand','Transactions')
<div class="container custom-container white-box py-2 text-center">
@endsection
@section("body")
<div id='txns' class="transactions mb-5">
    <div class="dropdown text-start d-flex justify-content-between">
        <button class="btn w-83 text-start bg-white mb-3 d-flex justify-content-between cust-drop-btn" type="button" id="myBondDrop" data-bs-toggle="dropdown" aria-expanded="false">
            <span id="drop_val">{{$selected_fund == null ? 'Filter by coupon' : $selected_fund}}</span>
            <span><i class="fas fa-caret-down"></i></span>
        </button>
        <a class="text-primary fs-6 ms-3 pt-2 {{$selected_fund == null ? 'd-none' : ''}}" href="/inv/transactions"> Clear </a>
    <ul class=" dropdown-menu dropdown-menu-end w-83" aria-labelledby="myBondDrop">
        @foreach($dist_bonds as $bond)
            <li><a class="dropdown-item {{$selected_fund == $bond ? 'selected-bond' : ''}}" href="/inv/transactions/{{$bond}}">{{$bond}}</a></li>
        @endforeach
    </ul>
    </div>
@foreach($bonds as $bond)
    <a href="/inv/bonds/{{$bond->fund_code}}" >
            <div class="his-div mb-2 row pt-3" style="background-color: {{$color_code[$bond->fund_code]}}">
                <div class="row cust-mb-8">
                    <div class="col-7 text-start"><h5>{{$bond->fund_code}}</h5></div><div class="col-4"><h5 class="text-right">{{$currency}} {{number_format($bond->amount)}}</h5></div><div class="col-1"><h5><i class="{{$bond->txn_type == 'investment' ? 'fas fa-arrow-right' : 'fas fa-arrow-left'}}" ></i></h5></div>
                </div>
                <div class="row">
                    <div class="col-6"><p class="text-start cust-gray">{{dd_value($bond->fund_type)}}</p></div>
                    <div class="col-5"><p class="text-right text-capitalize cust-gray">{{$bond->txn_type}}</p></div>
                </div>
                <div class="row cust-mb-14">
                    <div class="col text-start"><p class="mb-0 date-label d-inline-block">Realisation Date </p><a class="px-2 fs-6" tabindex="0" role="button" data-toggle="txn-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="{{$realisation_arr[$bond->txn_type]}}"><i class="fas fa-info-circle"></i></a><p class="text-start mt-0 date">{{(new Carbon\Carbon($bond->realisation_date))->format("D, d M Y")}}</p></div>
                    <div class="col text-end"><p class="mb-0 date-label d-inline-block">Transaction Date </p><a class="ps-2 fs-6" tabindex="0" role="button" data-toggle="txn-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="{{$txns_arr[$bond->txn_type]}}"><i class="fas fa-info-circle"></i></a><p class="text-end mt-0 date">{{(new Carbon\Carbon($bond->txn_date))->format("D, d M Y")}}</p></div>
                </div>
            </div>
    </a>
            @endforeach
</div>
    <script>
        $(document).ready(function () {
            @if(count($bonds) > 1)
            $('#drop_val').addClass('text-secondary');
            @endif
        });
    </script>
@endsection