<div>
    @php
        function status($tf_status,$clsname,$sbar = false){
            if($sbar){
                if(strcmp($tf_status,$clsname) > 0){
                    return "img/lead/finished.png";
                }
                elseif(strcmp($tf_status,$clsname) < 0){
                    return "img/lead/pending.png";
                }
                else{
                    return "img/lead/crnt.png";
                }
            }
            else{
                if($tf_status == $clsname){
                    return "";
                }
                else{
                    return "dis-input";
                }
            }
}
    @endphp
    <div class="leadCreatecontainer">
        @if (session()->has('updation'))
            <div id="alert" class="alert alert-success">
                {{ session('updation')}}
            </div>
        @endif
        <div class="d-flex justify-content-between p-3">
            <div class="createLeadtitle">
                <h1 class="no-margin">{{$title}} LEAD</h1>
            </div>
            <div class="createLeadtitle">
                <h1 class="no-margin">Status : {{get_tf_status($tf_status)}}</h1>
            </div>
        </div>
        <div class="ifScrollablecreateLead">
            <div id="tf_00_pending_biz_info" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Biz Info</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg {{$status}}" src="{{asset("img/lead/finished.png")}}" />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 dis-input">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Business Name / Cust Name</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.biz_name"><br/>
                                @error('leadDataArr.biz_name') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Mobile Number</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.mobile_num"><br/>
                                @error('leadDataArr.mobile_num') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">National ID</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.national_id"><br/>
                                @error('leadDataArr.national_id') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if(strcmp($status,'10_pending_rm_eval') < 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(0)">Edit</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="tf_00_pending_em_kyc" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">EzeeMoney KYC</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src="{{asset("img/lead/finished.png")}}" />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 dis-input ">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Abbrevation Name</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.UEZM_MainContent_txtAbbreviationName">
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Company Registration No</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.UEZM_MainContent_txtCompanyRegistrationNo">
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Recruiter ID</p>
                                <input class="w-75 loginInput" wire:model.defer="leadDataArr.UEZM_MainContent_txtRecruiterID">
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Nature Of Business <i class="arrow down"></i></p>
                                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model.defer="leadDataArr.UEZM_MainContent_ddlNatureOfBusiness">
                                    <option selected >choose</option>
                                    @foreach($natureDrop as $nature)
                                        <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Operated By</p>
                                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model.defer="leadDataArr.UEZM_MainContent_ddOperatedBy">
                                    <option selected >choose</option>
                                    @foreach($opertedbyDrop as $nature)
                                        <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Wallet Type</p>
                                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model.defer="leadDataArr.UEZM_MainContent_ddWallet">
                                    <option selected >choose</option>
                                    <option value=1>sc</option>
                                    <option value=2>Merchant Acct</option>
                                    <option value=3>SC+Merchant Acct</option>
                                </select>
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Zone</p>
                                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model.defer="leadDataArr.UEZM_MainContent_ddlZone">
                                    <option selected >choose</option>
                                    @foreach($zoneDrop as $nature)
                                        <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(strcmp($status,'10_pending_rm_eval') < 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(1)">Edit</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="{{$clsname[1]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Product</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(strcmp($tf_status,"tf_01_pending_dp") < 0 ? "img/lead/crnt.png" : "img/lead/finished.png")}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{strcmp($tf_status,$clsname[1]) == 0 ? "" : "dis-input"}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Product</p>
                                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model.defer="leadDataArr.product">
                                    <option selected value="">choose</option>
                                    {!! $productDrop !!}
                                </select>
                            </div>
                        </div>
                        @if(strcmp($tf_status,"10_pending_rm_eval") < 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(2)">Edit</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="{{$clsname[2]}}" class="row no-margin">
                <div id ="tf_01_pending_dp_ver" class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Down Payment</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[2],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[2])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Amount</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100" wire:model.defer ='update_data.downpayment.amount'></br>
                                    @if (session()->has('downpayment'))
                                        <span class="error text-danger">{{session('downpayment')}}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Payment Date</p>
                                <div class="dropdownPosition">
                                    <input class="w-100" type="date" id="start"  wire:model.defer ='update_data.downpayment.date'>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Transfer Receipt</p>
                                <div class="dropdownPosition w-100">
                                    @if(!$update_data['downpayment']['proof'])
                                        <input type="file" class="loginInput w-75" accept="image/*" wire:model ='txn_img'>
                                        @if ($txn_img)
                                            <button type="button" class="btn btn-sm btn-light ml-2" data-toggle="modal" data-target="#txnProof">
                                                view
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-sm btn-light ml-2" data-toggle="modal" data-target="#txnProof">
                                            view
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(strcmp($tf_status,$clsname[2]) == 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(3)">Save</button>
                            </div>
                        @endif
                        @if((session("user_role") == "super_admin" || session("user_role") == "app_support") && $this->tf_status == "tf_01_pending_dp_ver")
                            <div  class = "text-right">
                                <button type="button" class="verify-btn btn btn-sm btn-light ml-2" wire:click = "verify()">
                                    Verify Payment
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if(strcmp($tf_status,"tf_10_pending_sc_gen") < 0)
                <div class="mt-5 text-center">
                    <p class="text-white onboard fw-bold no-margin" class="no-margin">Further Update Requires KYC Approval from Flow</p>
                </div>
            @endif
            <div id="{{$clsname[3]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Flow KYC</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[3],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[3])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Flow Cust ID</p>
                                <input type='text' class="w-75 loginInput   " wire:model.defer="leadDataArr.cust_id" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="{{$clsname[4]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">SC Code Generation</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[4],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[4])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">SC code</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100" wire:model ='update_data.sc_code.sc_code'>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                @if((session("user_role") == "super_admin" || session("user_role") == "app_support"))
                                    <button class="scBtn mt-3 ml-4" wire:click="get_sc_code">Generate SC Code</button>
                                @endif
                            </div>
                        </div>
                        @if(strcmp($tf_status,$clsname[4]) == 0 && (session("user_role") == "super_admin" || session("user_role") == "app_support") )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(7)">Save</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="{{$clsname[5]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class = "col-10 pr-1">
                            <p class="statusText no-margin">Transfer Down Payment to SC Code </p>
                        </div>
                        <div class="col-1 no-padding">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[5],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[5])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Transfer Amount</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100" wire:model.defer ='update_data.transfer.amount'></br>
                                    @if (session()->has('downpayment'))
                                        <span class="error text-danger">{{session('downpayment')}}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Transfer Date</p>
                                <div class="dropdownPosition">
                                    <input class="w-100" type="date" id="start" wire:model.defer ='update_data.transfer.date'>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Transaction ID</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100" wire:model.defer ='update_data.transfer.txn_id'>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                            </div>
                        </div>
                        @if(strcmp($tf_status,$clsname[5]) == 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(8)">Save</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="{{$clsname[6]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Activate Terminal</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[6],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[6])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Terminal ID</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100" wire:model.defer ='update_data.activation.terminal_id'>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">

                            </div>
                        </div>
                        @if(strcmp($tf_status,$clsname[6]) == 0 )
                            <div class="text-right">
                                <button class=" updateLeadbtn" onclick="updatestatus(9)">Save</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="{{$clsname[7]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Loan Disbursal</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[7],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[7])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Status</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100 text-white terminalInput text-capitalize" wire:model.defer ='update_data.loan.status' disabled>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Amount</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100 text-white terminalInput" wire:model.defer ='update_data.loan.amount' disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Transaction ID</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100 text-white terminalInput" wire:model.defer ='update_data.loan.txn_id' disabled>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Date</p>
                                <div class="dropdownPosition">
                                    <input type="date" class="loginInput w-100 text-white terminalInput" wire:model.defer ='update_data.loan.date' disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="{{$clsname[8]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Hand over POS to Flow RM </p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[8],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[8])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Flow RM</p>
                                <div class="dropdownPosition">
                                    <input class="loginInput w-100 text-white terminalInput text-capitalize" wire:model.defer ='leadDataArr.flow_rm' disabled>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">RM Hand Over Date</p>
                                <div class="dropdownPosition">
                                    <input class="w-100" type="date" id="start" wire:model.defer ='update_data.rm_handover.date'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="{{$clsname[9]}}" class="row no-margin">
                <div class="col-md-1 no-padding statusBorder">
                    <div class="statusBox row">
                        <div class="col-10 pr-1">
                            <p class="statusText no-margin" class="no-margin">Hand over POS to customer</p>
                        </div>
                        <div class="col-1">
                            <img class="statusImg" src='{{asset(status($tf_status,$clsname[9],true))}}' />
                        </div>
                    </div>
                </div>
                <div class="col-md-11 pt-4 {{status($tf_status,$clsname[9])}}">
                    <div class="leadCreatecontainerOne">
                        <div class="row no-margin leadNamecontainerInsidepad">
                            <div class="col-md-6 no-padding">
                                <p class="no-margin lightText">Customer Hand Over Date</p>
                                <div class="dropdownPosition">
                                    <input class="w-100" type="date" id="start" wire:model.defer ='update_data.cust_handover.date'>
                                </div>
                            </div>
                            <div class="col-md-6 no-padding">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($tf_status == 'tf_50_pending_repay_cycle')
                <div id="tf_50_pending_repay_cycle" class="mt-5 text-center">
                    <p class="text-white onboard fw-bold no-margin" class="no-margin">Customer onboarded</p>
                </div>
            @endif
        </div>
        <div class="leadCreatebuttonBox">
            <button class="btn-danger" wire:click="toHome">CLOSE</button>
        </div>

    </div>
    <script>
        var clsname = ['tf_00_pending_biz_info','tf_00_pending_em_kyc','tf_01A_pending_prod_sel','tf_01_pending_dp','tf_01_pending_dp_ver','tf_02A_pending_rm_alloc','tf_02_pending_flow_kyc','tf_10_pending_sc_gen','tf_10_pending_transfer_dp','tf_20_pending_terminal_act','tf_30_pending_flow_disb','tf_40_pending_pos_to_rm','tf_50_pending_pos_to_cust','tf_50_pending_repay_cycle'];
        var idnxt;
        var id = "#" + "{{$tf_status}}"
        $(id + " .updateLeadbtn").html('Save')
        function updatestatus(num) {
            var update = true;
            var $idcrnt = $('#' + clsname[num]);
            var nowDiv = "#" + clsname[num] + " .col-md-11";
            var nowBtn = "#" + clsname[num] + " .updateLeadbtn";
            if ($(nowBtn).html() == "Save") {
                $.each($idcrnt.find("input"), function (i, input) {
                    if ($(input).val().length == 0 || $.trim($(input).val()) == '') {
                        update = false;
                    }
                });
                if (update == true) {
                    if (num < 12) {
                        var cnfrm;
                        var idcrnt = '#' + clsname[num];
                        if ("{{$tf_status}}" <= clsname[num]) {
                            idnxt =  clsname[num + 1];
                        } else {
                            idnxt = '{{$tf_status}}'
                        }
                        if (idnxt == undefined) {
                            alert("No Updates to Save")
                        } else {
                            if ("{{$tf_status}}" < "tf_01_pending_dp") {
                                cnfrm = confirm("Confirm Update?");

                            } else {
                                cnfrm = confirm("You can't change once updated. Confirm Update?");
                            }
                        }
                        if (cnfrm) {

                            Livewire.emit('saveUpdate', idnxt);
                        }
                    }
                } else {
                    alert("Enter the required fields to update!");
                }
            }
            else{
                $(nowBtn).html("Save");
                $(nowBtn).addClass("btnGreen")
                $(nowDiv).removeClass("dis-input");
                $(nowDiv + " input").prop("disabled",false);
            }
        }
        $('.ifScrollablecreateLead').animate({
            scrollTop: $("#" + "{{$tf_status}}").offset().top - 200
        }, 1000);

    </script>
    <div>
        <div class="modal fade" id="txnProof" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Photo Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @if($txn_img)
                        <img src="{{$txn_img->temporaryUrl()}}" alt="img">
                    @endif
                    @if($update_data['downpayment']['proof'] && !$txn_img)
                        <img src="{{asset($update_data['downpayment']['proof'])}}"
                    @endif
                </div>
            </div>
        </div>
    </div>
{{--<!-- <div class="modal fade" id="verifyPmnt" tabindex="-1" role="dialog" aria-labelledby="exampleTitle" aria-hidden="true" >--}}
{{--        <div class="modal-dialog modal-lg modal-lg-cust modal-dialog-centered" role="document">--}}
{{--            <div class="modal-content text-white">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="verification Title">Verify Payment</h5><span class="text-danger"> *</span>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                    @if (session()->has('verify'))--}}
{{--    <div id="alert" class="alert alert-success">--}}
{{--{{ session('verify')}}--}}
{{--            </div>--}}
{{--@endif--}}
{{--        </div>--}}
{{--@if($update_data['downpayment']['proof'] && !$txn_img)--}}
{{--    <div class = "leadCreatecontainer no-margin">--}}
{{--        <div class = "d-inline-block img-sec">--}}
{{--                <img src="{{asset($update_data['downpayment']['proof'])}}"/>--}}
{{--                    </div>--}}
{{--                    <div class ="d-inline-block info-sec ">--}}
{{--                        <h4>Payment Verification</h4>--}}
{{--                            <div class="pb-3">--}}
{{--                                <p class="no-margin lightText d-inline">Verifier Name</p><span class="text-danger"> *</span>--}}
{{--                                <div class="dropdownPosition">--}}
{{--                                    <input class="loginInput" wire:model.defer ='update_data.verification.name'></br>--}}
{{--                                    @error('update_data.verification.name') <span class="error text-danger">{{ $message }}</span> @enderror--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    <div>--}}
{{--        <p class="no-margin lightText d-inline">Transaction ID (Should match with time)</p><span class="text-danger"> *</span>--}}
{{--            <input class ="custom-select w-75 loginInput custom-select-sm no-padding" type="text" list="txns" wire:model.defer = "update_data.verification.txn_id"/><br>--}}
{{--@error('update_data.verification.txn_id') <span class="error text-danger">{{ $message }}</span> @enderror--}}
{{--            <datalist id="txns">--}}
{{--@foreach($txns as $txn)--}}
{{--        <option value={{$txn->stmt_txn_id}} >{{$txn->stmt_txn_date}}</option>--}}
{{--                                    @endforeach--}}
{{--            </datalist>--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <button type="button" class="btn btn-sm btn-light ml-2 vr-btn " wire:click = "verify" data-dismiss="modal" aria-label="Close">--}}
{{--            Verified--}}
{{--        </button>--}}
{{--    </div>--}}
{{--</div>--}}
{{--</div>--}}
{{--@endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div> -->--}}
</div>
