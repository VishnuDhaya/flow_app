@php
  $currency = get_currency_sign(session('currency_code'));
@endphp
@extends('investorsite.navbar')
@section('brand','')
@section('head')

  <div class="container custom-container white-box pt-2">
    @endsection
    @section('body')
      <div id="loader" class="d-none"><img src="{{asset("img/investorsite/flow_logo_spinner.png")}}"></div>
      <div class="main-div animate-bottom">
        <div class=" text-center pt-5">
          <h1 class="name mb-3"><b>Welcome, {{Auth::guard('investors')->user()->name}} !</b></h1>
          <a href="/inv/bonds/{{$bonds[0]->fund_code}}">
            <div class="dated-amount">
              <div class="row">
                <div class="col-sm-5 cust-col-5">
                  <p class="cus-fs">TOTAL INVESTED AMOUNT</p>
                  <h3 class="dis-in ">{{$currency}}{{number_format($total_inv_amt)}}</h3>
                </div>
                <div class="col-sm-1 cust-col-1 border-end"></div>
                <div class="col-sm-5 cust-col-5">
                  <p class="cus-fs">TOTAL VALUE ON {{$report_date}}</p>
                  <h3 class="mb-0 border-bottom d-inline-block">{{$currency}}{{number_format($total_earnings + $total_inv_amt)}}</h3>
                  <h6 class="return">Coupon {{$currency}}{{number_format($total_earnings)}}</h6>
                </div>
              </div>
              <div id="percent" class="perc">
                <p><i class="fas fa-arrow-up"></i>  {{number_format($total_annualized_returns, 2)}}% annualized returns</p>
              </div>
              <div class="dated-icon text-end"><i class="far fa-hand-point-right fs-5"></i></div>
            </div></a>
          <div class="hr-line">
          </div>
        </div>
        <div class="crnt-bond text-center">
          <h4 class="text-start p-2 mb-0 pb-0 fw-bold">My Current Bonds ({{count($bonds)}})</h4>
          <div id="carouselControls" class="carousel slide" data-bs-ride="carousel">
            <div id="carousel-div" class="carousel-inner cus-carousal-inner">
              @foreach($bonds as $bond)
                @if($bond->fund_type == 'variable_coupon')
                  <div class="carousel-item">
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
                            <h1 class="mb-0">{{$bond->profit_rate * 100}}%</h1>
                            <p class="mb-0">share in gross profits</p>
                          </div>
                          <div class="col-4 text-end pe-4">
                            <h1 class="mb-0">{{$bond->floor_rate * 100}}%</h1>
                            <p class="mb-0">floor rate</p>
                          </div>

                        </div>
                        <div class="hr-line">

                        </div>
                        <div class="row bond-amnt pt-2">
                          <div class="col-5 cust-col-5 px-0">
                            <p class="pb-0 mb-0">INVESTED AMOUNT</p>
                            <h4 class="dis-in">{{$currency}} {{number_format($bond->amount)}}</h4>
                          </div>
                          <div class="col-1 cust-col-1 border-end">
                          </div>
                          <div class="col-5 cust-col-5">
                            <p class="pb-0 mb-0">VALUE ON {{$report_date}}</p>
                            <h4 class="pt-0 mt-0">{{$currency}} {{number_format($bond->earning + $bond->amount)}}</h4>
                          </div>
                        </div>
                        <div id="percent">
                          <p><i class="fas fa-arrow-up"></i> {{number_format(get_annualized_returns($bond->amount, $bond->earning, $bond->alloc_date),2)}}% annualized return</p>
                        </div>
                      </div>
                    </a>
                  </div>
                @elseif($bond->fund_type == 'fixed_coupon')
                  <div class="carousel-item">
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
                            <h4 class="dis-in">{{$currency}} {{number_format($bond->amount)}}</h4>
                          </div>
                          <div class="col-1 cust-col-1 border-end">
                          </div>
                          <div class="col-5 cust-col-5">
                            <p class="pb-0 mb-0">VALUE ON {{$report_date}}</p>
                            <h4 class="pt-0 mt-0">{{$currency}} {{number_format($bond->earning + $bond->amount)}}</h4>
                          </div>
                        </div>
                        <div id="percent">
                          <p><i class="fas fa-arrow-up"></i> {{number_format($bond->profit_rate * 100,2)}}% annualized return</p>
                        </div>
                      </div>
                    </a>
                  </div>
                @endif
              @endforeach

            </div>
            @if(count($bonds) > 1)
              <button class="carousel-control-prev justify-content-start" type="button" data-bs-target="#carouselControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next justify-content-end" type="button" data-bs-target="#carouselControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            @endif
          </div>
          <div class="hr-line">
          </div>
        </div>
        <div class="social-rtn-head mt-2 ">
          <h4 class="mb-0 ps-2 fw-bold">Social Impact</h4>
        </div>
        <div class="social_rtn_tab_switch text-end">
          <button id="all-btn" class="btn scl-tab-btn active" type="button" data-bs-target="#carouselControlsHome" data-bs-slide-to="0"  aria-current="true" aria-label="Slide 1">ALL</button>
          <P class="d-inline-block"> | </P>
          <button id="men-btn" class="btn scl-tab-btn" type="button" data-bs-target="#carouselControlsHome" data-bs-slide-to="1" class="btn scl-btn" aria-label="Slide 2"><i class="fas fa-male"></i> ({{number_format($social_returns->male_perc*100)}}%)</button>
          <P class="d-inline-block"> | </P>
          <button id="women-btn" class="btn scl-tab-btn" type="button" data-bs-target="#carouselControlsHome" data-bs-slide-to="2" class="btn scl-btn" aria-label="Slide 3"><i class="fas fa-female"></i> ({{number_format( $social_returns->female_perc*100)}}%)</button>
        </div>
        <div id="carouselControlsHome" class="carousel slide"   data-bs-intervel=10000 data-bs-ride="carousel">
          <div id="carousel-div" class="carousel-inner cus-carousal-inner home-carousel">
            <div class="carousel-item all-tab active">
              <div class="row scl-smry mb-3 mx-1 pt-2">
                <div class="text-start social ">
                  <div class="row py-2 pe-0">
                    <div class="col-8 pe-0">
                      <p class="d-inline">Small businesses </p><a tabindex="0" role="button" data-toggle="info-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Number of small businesses which have been financed by Flow using the funds you invested."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p id="pb">
                        {{($social_returns->small_business)}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 ">
                      <p class="d-inline">Small business revenue catalyzed
                      </p><a tabindex="0" role="button" data-toggle="info-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The amount of business revenue which the small businesses have been able to generate through the funds you invested. For many Flow customers, this is the lionshare of household income, therefore benefitting entire families.  ."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p id="crc">
                        {{$currency}}{{number_format($social_returns->cust_revenue )}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 ">
                      <p class="d-inline">Number of txns facilitated
                      </p><a tabindex="0" role="button" data-toggle="info-popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The funds you invested enable Flow customers (the small businesses) to offer payment services in their shops. These include bill payments (e.g. electricity, school fees) and P2P transfers. This number illustrates the total number of transactions which were facilitated through your investment."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p id="rtf">
                        {{number_format($social_returns->num_of_txn )}}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item men-tab">
              <div class="row scl-smry mb-3 mx-1 pt-2">
                <div class="text-start social">
                  <div class="row py-2 pe-0">
                    <div class="col-8 pe-0">
                      <p class="d-inline">Small businesses </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Number of small businesses which have been financed by Flow using the funds you invested."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{number_format(($social_returns->small_business) * $social_returns->male_perc)}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 ">
                      <p class="d-inline">Small businesses revenue catalyzed
                      </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The amount of business revenue which the small businesses have been able to generate through the funds you invested. For many Flow customers, this is the lionshare of household income, therefore benefitting entire families.  "><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{$currency}}{{number_format(($social_returns->cust_revenue ) * $social_returns->male_perc)}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 ">
                      <p class="d-inline">Number of txns facilitated
                      </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The funds you invested enable Flow customers (the small businesses) to offer payment services in their shops. These include bill payments (e.g. electricity, school fees) and P2P transfers. This number illustrates the total number of transactions which were facilitated through your investment."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{number_format(($social_returns->num_of_txn ) * $social_returns->male_perc)}}
                      </p>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="carousel-item women-tab">
              <div class="row scl-smry mb-3 mx-1 pt-2">
                <div class="text-start social">
                  <div class="row py-2 pe-0">
                    <div class="col-8 pe-0">
                      <p class="d-inline">Small businesses </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="Number of small businesses which have been financed by Flow using the funds you invested."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{number_format(($social_returns->small_business) * $social_returns->female_perc)}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 pe-0">
                      <p class="d-inline">Small businesses revenue catalyzed
                      </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The amount of business revenue which the small businesses have been able to generate through the funds you invested. For many Flow customers, this is the lionshare of household income, therefore benefitting entire families.  "><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{$currency}}{{number_format(($social_returns->cust_revenue ) * $social_returns->female_perc)}}
                      </p>
                    </div>
                  </div>
                  <div class="row py-2">
                    <div class="col-8 ">
                      <p class="d-inline">Number of txns facilitated
                      </p><a tabindex="0" role="button" data-toggle="popover" data-bs-trigger="hover" data-bs-placement="bottom" data-bs-content="The funds you invested enable Flow customers (the small businesses) to offer payment services in their shops. These include bill payments (e.g. electricity, school fees) and P2P transfers. This number illustrates the total value of transactions which were facilitated through your investment."><i class="fas fa-info-circle"></i></a>
                    </div>
                    <div class="col-4 text-end">
                      <p>
                        {{number_format(($social_returns->num_of_txn ) * $social_returns->female_perc)}}
                      </p>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div>
          <div class="hr-line">

          </div>
          <h4 class="ps-2 pt-2 fw-bold">Current Flow Bonds</h4>
          <div class="cus-carousal">
            <div id="cus-owl-carousal" class="slider owl-carousel">
              @foreach($crnt_bonds as $crnt_bond)
                @if($crnt_bond->bond_type =="variable_coupon")
                  <div class="card me-3 var-box">
                    <div class="content px-2">
                      <h5 class="card-title fw-bolder c-head mb-0">Variable</h5>
                      <h5 class="card-title fw-bolder c-head mb-0">{{$crnt_bond->duration}} months</h5>
                      <h1 class="card-subtitle text-end fw-bolder c-body">{{str_replace(".00", "", (string) number_format($crnt_bond->profit_percent))}}%</h1>
                      <p class="prft text-end c-body mb-1">share in gross profit</p>
                      <p class="floor-prcnt m-0 text-end fs-5 c-body">{{str_replace(".00", "", (string) number_format($crnt_bond->floor_rate, 1))}}%</p>
                      <p class="floor text-end c-body">floor rate</p>
                    </div>
                  </div>
                @elseif($crnt_bond->bond_type=="fixed_coupon")
                  <div class="card me-3 fix-box">
                    <div class="content px-2">
                      <h5 class="card-title fw-bolder c-head mb-0">Fixed</h5>
                      <h5 class="card-title fw-bolder c-head mb-0">{{$crnt_bond->duration}} months</h5>
                      <h1 class="card-subtitle text-end fw-bolder c-body">{{str_replace(".00", "", (string) number_format($crnt_bond->profit_percent, 1))}}%</h1>
                      <p class="prft text-end c-body">Fixed return</p>
                    </div>
                  </div>
                @elseif($crnt_bond->bond_type=="platinum_coupon")
                  <div class="card me-3 plat-box">
                    <div class="content">
                      <h5 class="card-title">Platinum</h5>
                      <h1 class="card-subtitle text-end fw-bolder c-body">{{str_replace(".00", "", (string) number_format($crnt_bond->profit_percent, 2))}}%</h1>
                      <p class="prft text-end">share in gross profit</p>
                      <p class="floor-prcnt m-0 fs-5 text-end">{{str_replace(".00", "", (string) number_format($crnt_bond->floor_rate,2))}}%</p>
                      <p class="floor text-end">floor rate</p>
                    </div>
                  </div>
                @elseif($crnt_bond->bond_type=="diamond_coupon")
                  <div class="card me-3 dmnd-box">
                    <div class="content">
                      <h5 class="card-title">Diamond</h5>
                      <h1 class="card-subtitle text-end fw-bolder c-body">{{number_format($crnt_bond->profit_percent)}}%</h1>
                      <p class="prft text-end">share in gross profit</p>
                      <p class="floor-prcnt m-0 fs-5 text-end">{{number_format($crnt_bond->floor_rate)}}%</p>
                      <p class="floor text-end">floor rate</p>
                    </div>
                  </div>
                @endif
              @endforeach
              <div class="ref">
                <!-- <p>
                  END OF BOND <BR /> <<<<<<
                </p> -->
              </div>
            </div>
            <script>
              $(".slider").owlCarousel({
                loop: false,
                autoplay: false,
                items:3
              });
            </script>
          </div>
        </div>
        <div class="ps-2 pt-2 pb-5 invs-guide">
          <div class="invs-head">
            <h3 ><b>Investment Guide</b></h3>
          </div>
          <div class="invs invs-g-1">
            <h6><b>What is variable-rate bond?</b></h6>
            <div class="row">
              <div class="col-8" >
                <p>This offers a 2.5% minimum ('floor') rate + a potential upside of 40% of the gross profits generated with the monies invested. Gross profits = revenues - platform - commissions - NPLs. </p>
              </div>
              <div class="img-col col-4" >
                <img src="{{asset("img/investorsite/gold-img.jpg")}}" alt="">
              </div>
            </div>
          </div>
          <div class="invs invs-g-1">
            <h6><b>What is fixed-rate bond?</b></h6>
            <div class="row">
              <div class="col-8" >
                <p>This offers a fixed rate. Rates vary based on the investment amount and maturity.</p>
              </div>
              <div class="img-col col-4" >
                <img src="{{asset("img/investorsite/silver-img.jpg")}}" alt="">
              </div>
            </div>
          </div>
        </div>
        <script src="{{asset('js/countUp.min.js')}}"></script>
        <script>
          const options = {
            prefix: '{{$currency}}',

          };
          if (!sessionStorage.alreadyClicked) {
            duration = 5;
          }
          else{
            duration = 2;
          }
          let crc = new CountUp('crc', {{($social_returns->cust_revenue ) - 1000}}, {{$social_returns->cust_revenue }}, 0, duration, options);
          let rtf = new CountUp('rtf', {{($social_returns->tot_retail_txn_value ) - 1000}}, {{($social_returns->num_of_txn )}}, 0, duration);
          let pb = new CountUp('pb', 0, {{($social_returns->small_business)}}, 0, duration);
            crc.start();
            rtf.start();
            pb.start();

        </script>
@endsection


