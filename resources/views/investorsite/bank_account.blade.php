
@section('head')
    @extends('investorsite.navbar')
@section('brand','Bank Account')
<div class="container custom-container white-box py-2 mb-5 overflow-auto">
    @endsection
    @section("body")
        <div class="top-space pb-5 px-2">
            <div id="no_acc" class="center-box text-center w-100 pe-5 {{$bank_data->country == null ? '' : 'd-none'}}">
                <div class="rounded-circle cust-round">
                    <span>!</span>
                </div>
                <div class="info-text">
                    <p>No bank account linked.</p>
                </div>
                <div>
                    <button class="btn btn-success cust-btn" onclick = "add_acc()"> Add Bank Account </button>
                </div>
            </div>
            <form id="acc_form" class ="{{$bank_data->country != null ? 'no-inputs' : 'd-none acc_form'}}" action="{{ route('add_acc') }}" method="post">
                @csrf
            <div id="bank">
                <div>
                <div class="d-flex justify-content-between">
                    <p class="form-sub-head"><i class="fas fa-university"></i> Bank Info</p>
                    <div onclick="edit('#bank')"><i class="fas fa-edit fs-5 {{$bank_data->country != null ? '' : 'd-none'}}"></i></div>
                </div>
                <div class="form-input">
                    <label>Country (where the account is registered)</label>
                    <input type="text" name="country"  value="{{$bank_data->country}}" required/>
                </div>
                @if($currency == 'USD')
                <div class="form-input">
                    <label>Account Number</label>
                    <input id="acc" type="text" name="usd_account_num" value="{{$bank_data->usd_account_num}}" required/>
                </div>
                <div class="form-input {{$bank_data->usd_account_num != null ? 'd-none cnf-cls' : ''}}">
                    <label>Confirm Account Number</label>
                    <input id="acc_cnfm" type="text" value="{{$bank_data->usd_account_num}}" required/>
                </div>
                <div class="form-input">
                    <label>ACH Routing Number</label>
                    <input type="text" name="usd_ach_routing_num" value="{{$bank_data->usd_ach_routing_num}}" required/>
                </div>
                @else
                    <div class="form-input">
                        <label>IBAN</label>
                        <input id="acc" type="text" name="eur_iban" value="{{$bank_data->eur_iban}}" onpaste="return false;" required/>
                    </div>
                    <div class="form-input {{$bank_data->eur_iban != null ? 'd-none cnf-cls' : ''}}">
                        <label>Confirm IBAN</label>
                        <input id="acc_cnfm" type="text"  value="{{$bank_data->eur_iban}}" required/>
                    </div>
                    <div class="form-input">
                        <label>BIC</label>
                        <input type="text" name="eur_bic" value="{{$bank_data->eur_bic}}" required/>
                    </div>
                @endif
                <div class="form-input">
                    <label>Bank / Payment Institution</label>
                    <input type="text" name="institution" value="{{$bank_data->institution}}" required/>
                </div>
                <div class="form-input">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{$bank_data->first_name}}" required/>
                </div>
                <div class="form-input">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{$bank_data->last_name}}" required/>
                </div>
            </div>
            </div>
                <div id="rsdnt">
                    <div class="d-flex justify-content-between">
                    <p class="form-sub-head mt-2"><i class="fas fa-home"></i> Residential Address</p>
                    <div onclick="edit('#rsdnt')"><i class="fas fa-edit fs-5 {{$bank_data->country != null ? '' : 'd-none'}}"></i></div>
                    </div>
                    <div class="form-input">
                        <label>Address Line 1</label>
                        <input type="text" name="address_line_1" value="{{$bank_data->address_line_1}}" required/>
                    </div>
                    <div class="form-input">
                        <label>Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{$bank_data->address_line_2}}"/>
                    </div>
                    <div class="form-input">
                        <label>City</label>
                        <input type="text" name="city" value="{{$bank_data->city}}" required/>
                    </div>
                    <div class="form-input">
                        <label>Postcode</label>
                        <input type="text" name="postcode" value="{{$bank_data->postcode}}" required/>
                    </div>
                </div>
                <div id="sub-btn" class="text-center {{$bank_data->country == null ? '' : 'd-none'}}"><button type="button" class="btn btn-success cust-btn my-2" onclick="confirm_acc()"> Add </button></div>
            </form>
        </div>
        <script>
            $(document).ready(function () {
                @if($bank_data->country)
                $("#bank input").prop('disabled',true);
                $("#rsdnt input").prop('disabled',true);
                @endif
            });

            function edit(sctn){
                $(sctn +" input").prop('disabled',false);
                $(sctn +" .fa-edit").addClass('d-none');
                $(sctn +" input")[0].focus();
                $('#sub-btn').removeClass('d-none');
                $(sctn +' .cnf-cls').removeClass('d-none');
                $("#sub-btn button").text('save');
            }
            function add_acc() {
                $("#no_acc").addClass('d-none');
                $("#acc_form").removeClass('d-none');
            }
            function confirm_acc() {
                acc = $('#acc').val();
                acc_cnfm = $('#acc_cnfm').val();
                if (acc == acc_cnfm) {
                    $("#acc_form").submit();
                }
                else{
                    @if($currency == 'USD')
                    alert("Account number and confirm account number miss match");
                    @else
                    alert("IBAN and confirm IBAN miss match");
                    @endif
                    $("#acc_cnfm").focus();
                }
            }
        </script>
@endsection