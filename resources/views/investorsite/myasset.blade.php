@php
    $currency = get_currency_sign($bonds[0]->fe_currency_code);
$returnPercent = 'XX';
@endphp
@extends('investorsite.navbar')
@section('brand','My Assets')
@section('head')
    <div class="container custom-container white-box py-2 text-center">
        @endsection
        @section('body')
            <div class="dated-amount pt-4">
                <div class="row pt-4">
                    <div class="col-sm-5 cust-col-5">
                        <p class="cus-fs">INVESTED AMOUNT</p>
                        <h3 class="dis-in ">{{$currency}}{{number_format($total_inv_amt)}}</h3>
                    </div>
                    <div class="col-sm-1 cust-col-1 border-end"></div>
                    <div class="col-sm-5 cust-col-5">
                        <p class="cus-fs">VALUE ON {{$report_date}}</p>
                        <h3 class="mb-0 border-bottom d-inline-block">{{$currency}}{{number_format($total_earnings + $total_inv_amt)}}</h3>
                        <h6 class="return">Coupon {{$currency}}{{number_format($total_earnings)}}</h6>
                    </div>
                </div>
                <div id="percent" class="perc">
                    <p><i class="fas fa-arrow-up"></i> {{number_format($total_annualized_returns, 2)}}% annualized returns</p>
                </div>
            </div>
            <div class="hr-line">

            </div>
            <div class="crnt-bond">
                <h4 class="text-start p-2 mb-0 pb-0">Current Bonds ({{count($bonds)}})</h4>
                <div class="my-all-bonds ">
                @foreach($bonds as $bond)
                    @if($bond->fund_type == 'variable_coupon')
                            <a href="/inv/bonds/{{$bond->fund_code}}">
                                <div id="var-bond" class="var-box fw-bolder pt-2">
                                    <div class="row">
                                        <div class="col text-start ms-2 mb-0">
                                            <p class="m-0">{{$bond->fund_code}}</p>
                                            <H5>{{dd_value($bond->fund_type)}} - {{$bond->duration}} months</H5>
                                        </div>
                                    </div>
                                    <div class="row bond-percent">
                                        <div class="col-8 text-start ps-4">
                                            <h1>{{$bond->profit_rate * 100}}%</h1>
                                            <p class="mb-0">on book profits</p>
                                        </div>
                                        <div class="col-4 text-end pe-4">
                                            <h1>{{$bond->floor_rate * 100}}%</h1>
                                            <p class="mb-0">floor rate</p>
                                        </div>

                                    </div>
                                    <div class="hr-line">

                                    </div>
                                    <div class="row bond-amnt pt-2">
                                        <div class="col-5 cust-col-5 px-0">
                                            <p class="pb-0 mb-0">INVESTED AMOUNT</p>
                                            <h4 class="dis-in">{{$currency}} {{number_format($bond->inv_amount)}}</h4>
                                        </div>
                                        <div class="col-1 cust-col-1 border-end">
                                        </div>
                                        <div class="col-5 cust-col-5">
                                            <p class="pb-0 mb-0">VALUE ON {{$report_date}}</p>
                                            <h4 class="pt-0 mt-0">{{$currency}} {{number_format($bond->earning + $bond->inv_amount)}}</h4>
                                        </div>
                                    </div>
                                    <div id="percent">
                                        <p><i class="fas fa-arrow-up"></i> {{get_annualized_returns($bond->inv_amount, $bond->earning, $bond->alloc_date)}}% annualized return</p>
                                    </div>
                                </div>
                            </a>
                    @elseif($bond->fund_type == 'fixed_coupon')
                            <a href="/inv/bonds/{{$bond->fund_code}}">
                                <div id="fix-bond" class="fix-box fw-bolder pt-2">
                                    <div class="row">
                                        <div class="col text-start ms-2 mb-0">
                                            <p class="m-0">{{$bond->fund_code}}</p>
                                            <H5 class="fw-bold">{{dd_value($bond->fund_type)}} - {{$bond->duration}} months</H5>
                                        </div>
                                    </div>
                                    <div class="row bond-percent">
                                        <div class="col-9 text-start ps-4">
                                            <h1 class="dis-in">{{$bond->profit_rate * 100}}% </h1><p class=" dis-in m-0 p-0"> fixed returns on the investment</p>
                                        </div>

                                    </div>
                                    <div class="hr-line">

                                    </div>
                                    <div class="row bond-amnt pt-2">
                                        <div class="col-5 cust-col-5 px-0">
                                            <p class="pb-0 mb-0">INVESTED AMOUNT</p>
                                            <h4 class="dis-in">{{$currency}} {{number_format($bond->inv_amount)}}</h4>
                                        </div>
                                        <div class="col-1 cust-col-1 border-end">
                                        </div>
                                        <div class="col-5 cust-col-5">
                                            <p class="pb-0 mb-0">VALUE ON {{$report_date}}</p>
                                            <h4 class="pt-0 mt-0">{{$currency}} {{number_format($bond->earning + $bond->inv_amount)}}</h4>
                                        </div>
                                    </div>
                                    <div id="percent">
                                        <p><i class="fas fa-arrow-up"></i> {{$bond->profit_rate * 100}}% annualized return</p>
                                    </div>
                                </div>
                            </a>
                    @endif
                @endforeach
                </div>
            </div>

@endsection

