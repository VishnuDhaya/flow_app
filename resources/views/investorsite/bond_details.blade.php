@php
    if($allocation){
    $currency = get_currency_sign(session('currency_code'));
    $fund_type = explode('_',$bond_details->fund_type)[0];
    }
@endphp

@section('head')
    <div id =variable-bond class="container custom-container white-box py-2 text-center">
        @endsection
        @extends('investorsite.navbar')
        @section('brand')
            <h5 class="navbar-brand mb-0 pb-0 me-4"><b>BOND DETAILS</b></h5>
            <H6 class="mt-0 pb-1 sub-head me-4">VALUE ON {{$report_date}}</H6>
        @endsection
        @section('body')
            <div class="bond-switch text-center mb-2">
                <div id="myBond" class="btn-group bg-white my-btn active-bond">
                    <button class="btn btn-sm pt-0" type="button" onclick="bondSwitch(this)">
                        My Bond <br>
                        {{$bond_details->fund_code}}
                    </button>
                    <button type="button" class="btn btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class=" dropdown-menu dropdown-menu-end" aria-labelledby="myBondDrop">
                        @foreach($my_bonds as $bond)
                            <li><a class="dropdown-item" href="/inv/bonds/{{$bond}}">{{$bond}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div id="allBonds" class="btn-group bg-white all-btn all-btn-div" onclick="bondSwitch(this)">
                <button type="button" class="btn all-btn" >All Flow Bonds</button>
                </div>
            </div>
            @if($allocation)
            <h3 class="bond-type-head mb-3 dis-in fs-3"><b>{{dd_value($fund_type)}} Bond</b></h3>
            <div class="single">
                <div class="row bond-detail-row dated-amount pt-1">
                    <div class="col-5">
                        @if($bond_details->fund_type=="variable_coupon")
                            <div class="card me-3 var-box">
                                <div class="content px-2">

                                    <h5 class="card-title fw-bolder text-start mb-0">Variable coupon</h5>
                                    <p class="mb-2 fs-6 py-0 text-start">({{$bond_details->duration}} months)</p>
                                    <h1 class="card-subtitle text-end fw-bolder c-body">{{($bond_details->profit_rate) * 100}}%</h1>
                                    <p class="prft text-end c-body">share in gross profit</p>
                                    {{-- <p class="floor-prcnt m-0 text-end fs-5 c-body">{{$bond_details->floor_rate}}%</p>
                                    <p class="floor text-end c-body">floor rate</p> --}}
                                </div>
                            </div>
                        @elseif($bond_details->fund_type=="fixed_coupon")
                            <div class="card me-3 fix-box">
                                <div class="content px-2">

                                    <h5 class="card-title fw-bolder text-start mb-0">Fixed coupon</h5>
                                    <p class="mb-2 fs-6 py-0 text-start">({{$bond_details->duration}} months)</p>
                                    <h1 class="card-subtitle text-end fw-bolder c-body">{{($bond_details->profit_rate) * 100}}%</h1>
                                    <p class="prft text-end c-body">Fixed return</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-7 text-start ps-4">
                        <p class="pb-0 mb-0 cus-fs">INVESTED AMOUNT</p>
                        <h3 class="py-0 mt-0 mb-3">{{$currency}}{{number_format($bond_details->invested_amount)}}</h3>
                        <p class="pb-0 mb-0 cus-fs">VALUE ON {{$report_date}}</p>
                        <h3 class="pt-0 mt-0 mb-1 border-bottom d-inline-block">{{$currency}}{{number_format($bond_details->earnings + $bond_details->invested_amount)}}</h3>
                        <h6 class="return fs-6 mb-2">Coupon {{$currency}}{{number_format($bond_details->earnings)}}
                            @if($bond_details->fund_type == 'variable_coupon')
                            <a id="returnpop" tabindex="0" role="button" data-toggle="return-popover" data-bs-trigger="hover" data-bs-placement="bottom"><i class="fas fa-info-circle"></i></a>
                        @endif
                        </h6>
                        <div id="percent" class="perc">
                            <p><i class="fas fa-arrow-up"></i>  {{ ($fund_type != 'fixed') ? get_annualized_returns($bond_details->invested_amount, $bond_details->earnings, $bond_details->alloc_date) : $bond_details->profit_rate * 100}}% annualized return</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="multi d-none">
                <div class="row bond-detail-row dated-amount pt-1">
                    <div class="col-5 pe-0">
                        <div id="carouselCard" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                            <div id="carousel-div" class="carousel-inner">
                                @foreach($bonds as $bond)
                                    @if($bond->fund_type == 'variable_coupon')
                                        <div class="carousel-item">
                                            <div class="card me-3 var-box">
                                                <div class="content px-2">
                                                    <p class="mb-0 py-0 text-start">{{$bond->fund_code}}</p>
                                                    <p class="mb-0 py-0 text-start">{{$bond->duration}} months</p>
                                                    <h5 class="card-title fw-bolder text-start">Variable Coupon</h5>
                                                    <h1 class="card-subtitle text-end fw-bolder c-body">{{($bond->profit_rate) * 100}}%</h1>
                                                    <p class="prft text-end c-body">share in gross profit</p>
                                                    {{-- <p class="floor-prcnt m-0 text-end fs-5 c-body">{{$bond_details->floor_rate}}%</p>
                                                    <p class="floor text-end c-body">floor rate</p> --}}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($bond->fund_type=="fixed_coupon")
                                        <div class="carousel-item">
                                            <div class="card me-3 fix-box">
                                                <div class="content px-2">
                                                    <p class="mb-0 py-0 text-start">{{$bond->fund_code}}</p>
                                                    <p class="mb-0 py-0 text-start">{{$bond->duration}} months</p>
                                                    <h5 class="card-title fw-bolder text-start">Fixed Coupon</h5>
                                                    <h1 class="card-subtitle text-end fw-bolder c-body">{{($bond->profit_rate) * 100}}%</h1>
                                                    <p class="prft text-end c-body">Fixed return</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-7 text-start ps-4">
                        <p class="pb-0 mb-0 cus-fs">INVESTED AMOUNT</p>
                        <h3 class="py-0 mt-0 mb-3">{{$currency}}{{number_format($all_bond_details->invested_amount)}}</h3>
                        <p class="pb-0 mb-0 cus-fs">VALUE ON {{$report_date}}</p>
                        <h3 class="pt-0 mt-0 mb-1 border-bottom d-inline-block">{{$currency}}{{number_format($all_bond_details->earnings + $all_bond_details->invested_amount)}}</h3>
                        <h6 class="return fs-6 mb-2">Coupon {{$currency}}{{number_format($all_bond_details->earnings)}}</h6>

                        <div id="percent" class="perc">
                            <p><i class="fas fa-arrow-up"></i> {{number_format($all_bond_details->annualized_returns, 2)}}% annualized return</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="return-content" class="return-div">
                <div class="return-popover">
                    <div class="row">
                        <div class="col-8">
                            <p>Gross Profits</p>
                        </div>
                        <div class="col-4">
                            <p>{{$currency}}{{number_format($bond_details->net_returns)}}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <p class="mb-0">Coupon</p>
                            <p class="popover-subhead">({{$bond_details->profit_rate * 100}}% share in gross profit)</p>
                        </div>
                        <div class="col-4">
                            <p>{{$currency}}{{number_format($bond_details->net_returns * $bond_details->profit_rate)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-1 pb-2 px-3">
                <div class="hr-line">

                </div>
            </div>
            <div class="tabs text-center">
                <button id="financial" type="button" data-bs-target="#carouselControls" data-bs-slide-to="0" class="btn fin-btn active" onclick="tabswitch(this)">Financial</button>
                <button id="social" type="button" data-bs-target="#carouselControls" data-bs-slide-to="1" class="btn scl-btn" onclick="tabswitch(this)">Social</button>
            </div>
            <div id="carouselControls" class="carousel slide" data-bs-wrap="false" data-bs-touch="false">
                <div id="carousel-div" class="carousel-inner cus-carousal-inner">
                    <div class="carousel-item mb-2 active">
                        <div  class="row bond-smry mb-5 pb-1 mx-1">
                            <div class="finances ">
                                <div class="row mt-2 pb-3">
                                    <div class="col">
                                        <h3 class="bond-smry-head pb-0 mb-0 d-inline">Financial</h3>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col tot-val mt-2">
                                        <h6>TOTAL DISBURSED</h6>
                                        <h1>{{$currency}}{{number_format($bond_details->total_disbursed)}}</h1>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col business-val text-end p-1 m-0 text-center">
                                        <h6>SMALL BUSINESSES</h6>
                                        <h1>{{number_format($bond_details->current_alloc_cust)}}</h1>
                                    </div>
                                </div>

                                @if($bond_details->fund_type=='variable_coupon')
                                    <table class="table mb-0">
                                        <tr class="pls">
                                            <th colspan="2">Fee earned </th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->fee_earned,2)}} +</td>
                                        </tr>
                                        <tr class="mns">
                                            <th colspan="2">Platform <a tabindex="0" role="button" data-toggle="smry-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Flow Uganda pays Flow Global a fee for using the technology platform: {{$bond_details->license_rate * 100}}% of the fees Flow Uganda earns."><i class="fas fa-info-circle"></i></a></th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->license_rate * $bond_details->fee_earned,2)}} -</td>
                                        </tr>
                                        <tr class="mns">
                                            <th colspan="2">Commissions <a tabindex="0" role="button" data-toggle="smry-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Flow pays commissions to field staff who visit the small businesses as well as to distribution partners (e.g. for the use of the payment platform)."><i class="fas fa-info-circle"></i></a></th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->comm,2)}} -</td>
                                        </tr>
                                        <tr class="mns">
                                            <th colspan="2">NPLs <a tabindex="0" role="button" data-toggle="smry-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="This is the amount Flow uses due to non-performing loans."><i class="fas fa-info-circle"></i></a></th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->bad_debts,2)}} -</td>
                                        </tr>
                                        <tr class="pls">
                                            <th colspan="2">NPLs Recovered <a tabindex="0" role="button" data-toggle="smry-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="This is the amount Flow has recovered from non-performing loans."><i class="fas fa-info-circle"></i></a></th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->bad_debts_recovered,2)}} +</td>
                                        </tr>
                                        <tr class="net-rtrn fw-bold pls">
                                            <th class="fw-bold" colspan="2">GROSS PROFITS</th>
                                            <td>{{$currency}}</td>
                                            <td class='text-right'>{{number_format($bond_details->net_returns)}}</td>
                                        </tr>
                                    </table>
                                    @endif
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item mb-2">
                        <div class="row bond-smry mb-5 pb-3 mx-1">
                            <div class="text-start social">
                                <div class="row mt-2 pb-3">
                                    <div class="col-12 text-center">
                                        <h3 class="bond-smry-head d-inline pb-0 mb-0">Social</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <p>Small Businesses <a tabindex="0" role="button" data-toggle="scl-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Number of small businesses which have been financed by Flow using the funds you invested."><i class="fas fa-info-circle"></i></a>
                                        </p>
                                    </div>
                                    <div class="col-4 text-end pb-val">
                                        <p>
                                            {{number_format($bond_details->small_business)}}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <p>Small businesses revenue catalyzed <a tabindex="0" role="button" data-toggle="scl-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The amount of business revenue which the small businesses have been able to generate through the funds you invested. For many Flow customers, this is the lionshare of household income, therefore benefitting entire families.  "><i class="fas fa-info-circle"></i></a>
                                        </p>
                                    </div>
                                    <div class="col-4 text-end crc-val">
                                        <p>
                                            {{$currency}}{{number_format($bond_details->cust_revenue)}}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 ">
                                        <p>Number of txns facilitated <a tabindex="0" role="button" data-toggle="scl-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The funds you invested enable Flow customers (the small businesses) to offer payment services in their shops. These include bill payments (e.g. electricity, school fees) and P2P transfers. This number illustrates the total number of transactions which were facilitated through your investment."><i class="fas fa-info-circle"></i></a>
                                        </p>
                                    </div>
                                    <div class="col-4 text-end rtf-val">
                                        <p>
                                            {{number_format($bond_details->num_of_txn)}}
                                        </p>
                                    </div>
                                </div>
                            </div><div id="photocarousel" class="carousel slide photo-carousel mb-2" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button class="photo-btn active" type="button" data-bs-target="#photocarousel" data-bs-slide-to="0" aria-current="true" aria-label="Slide 1"></button>
                                    <button class="photo-btn" type="button" data-bs-target="#photocarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                    <button class="photo-btn" type="button" data-bs-target="#photocarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                    <button class="photo-btn" type="button" data-bs-target="#photocarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="{{asset("img/investorsite/cust1.jpg")}}" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{asset("img/investorsite/cust2.jpg")}}" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{asset("img/investorsite/cust3.jpg")}}" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{asset("img/investorsite/cust4.jpg")}}" class="d-block w-100" alt="...">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <div class="d-none all-values">
        <div class="all-cust">{{number_format($all_bond_details->current_alloc_cust)}}</div>
        <div class="all-disb">{{$currency}}{{number_format($all_bond_details->total_disbursed)}}</div>
        <div class="all-crc">{{$currency}}{{number_format($all_bond_details->social_returns->cust_revenue)}}</div>
        <div class="all-rtf">{{number_format($all_bond_details->social_returns->num_of_txn)}}</div>
        <div class="all-pb">{{number_format($all_bond_details->social_returns->small_business)}}</div>
        <div class="cust">{{number_format($bond_details->current_alloc_cust)}}</div>
        <div class="disb">{{$currency}}{{number_format($bond_details->total_disbursed)}}</div>
        <div class="crc">{{$currency}}{{number_format($bond_details->cust_revenue)}}</div>
        <div class="rtf">{{number_format($bond_details->num_of_txn)}}</div>
        <div class="pb"> {{number_format($bond_details->small_business)}}</div>
        <div class="bond-head">{{dd_value($fund_type)}} Bond</div>
        <div class="fund_code">{{$bond_details->fund_code}}</div>
    </div>
    @else
                <div class="top-space pb-5 px-2">
                    <div class="center-box text-center w-100 pe-5" >
                        <div>
                            <img src="{{asset("img/investorsite/calender.png")}}" width="75%"/>
                        </div>
                        <div class="info-text">
                            <p><b>Your coupon has been allocated successfully!</b><br>

                            However, its summary will be available only when it completes one month</p>
                        </div>
                    </div>
                </div>
    @endif
@endsection
