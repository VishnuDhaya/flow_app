<div class="leadNamecontainer">
    <script>
        function deleting(){
            if(!$('#deletion').hasClass('confirm')){
                $("#deletion").html('CONFIRM');
                $("#deletion").addClass('confirm');
            }
            else{
                Livewire.emit('delete');
            }
        }
    </script>
    @if($user_id)
        <span class="is-rej stamp is-nope" {{$btn_view['rejected']}}>Rejected</span>
        <span class="is-closed stamp is-nope {{$profile_status == "closed" ? "" : "open"}}" >CLOSED</span>
        <div class="leadNamecontainerHeader">
            <h1 class="no-margin text-uppercase">{{$leadData['biz_name']}}</h1>
        </div>
        <div class="verticalHeightadj">
            <div class="">
                <div class="leadNamecontainerOne profileBoxOne">
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Account Number / SC Code</p>
                            <p class="darkText no-margin">{{$leadData['account_num']}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Mobile Number</p>
                            <p class="darkText no-margin">{{$leadData['mobile_num']}}</p>
                        </div>
                    </div>
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">National ID</p>
                            <p class="darkText no-margin">{{$leadData['national_id']}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Flow RM</p>
                            <p class="darkText no-margin">{{$leadData['flow_rm']}}</p>
                        </div>
                    </div>
                </div>
                <div class="leadNamecontainerOne profileBoxtwo">
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Product</p>
                            @if($leadData['product'])
                                <p class="darkText no-margin">{{$leadData['product']['amount']}} UGX | {{$leadData['product']['duration']}} {{$leadData['product']['duration_type']}} @ {{$leadData['product']['daily_deductions']}} UGX / day</p>
                            @endif
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Flow Cust ID</p>
                            <p class="darkText no-margin">{{$leadData['cust_id']}}</p>
                        </div>
                    </div>
                </div>
                <div class="leadNamecontainerOne profileBoxtwo">
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Status</p>
                            <p class="darkText no-margin">{{get_tf_status($leadData['tf_status'])}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Terminal ID</p>
                            <p class="darkText no-margin">{{$update_data['activation']['terminal_id']}}</p>
                        </div>
                    </div>
                </div>
                <div class="leadNamecontainerOne leadNamecontainerOnenoMarginBot profileBoxThree">
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Abrrevation Name</p>
                            <p class="darkText no-margin">{{$leadData['UEZM_MainContent_txtAbbreviationName']}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Company Registration No</p>
                            <p class="darkText no-margin">{{$leadData['UEZM_MainContent_txtCompanyRegistrationNo']}}</p>
                        </div>
                    </div>
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Recruiter ID</p>
                            <p class="darkText no-margin">{{$leadData['UEZM_MainContent_txtRecruiterID']}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Nature Of Business</p>
                            <p class="darkText no-margin">{{$this->dropValue('UEZM_MainContent_ddlNatureOfBusiness',$leadData['UEZM_MainContent_ddlNatureOfBusiness'])}}</p>
                        </div>
                    </div>
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Operated By</p>
                            <p class="darkText no-margin">{{$this->dropValue('UEZM_MainContent_ddOperatedBy',$leadData['UEZM_MainContent_ddOperatedBy'])}}</p>
                        </div>
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Wallet Type</p>
                            <p class="darkText no-margin">{{$this->dropValue('UEZM_MainContent_ddWallet',$leadData['UEZM_MainContent_ddWallet'])}}</p>
                        </div>
                    </div>
                    <div class="row no-margin leadNamecontainerInsidepad">
                        <div class="col-md-6 no-padding">
                            <p class="lightText no-margin">Zone</p>
                            <p class="darkText no-margin">{{$this->dropValue('UEZM_MainContent_ddlZone',$leadData['UEZM_MainContent_ddlZone'])}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if($profile_status == "open")
            <div class="leadButtonbox">
                <button id='deletion' class="btnRed .deletion" {{$btn_view['edit_delete']}} onclick="deleting()">DELETE</button>
                <button class="btnWhite" {{$btn_view['edit_delete']}} wire:click="tosite('leadupdate',{{$user_id}},'EDIT')">EDIT</button>
                <button class="btn-large btnWhite" {{$btn_view['chg_prdct']}} wire:click="tosite('leadupdate',{{$user_id}},'EDIT')">EDIT</button>
                <button class="btnWhite" {{$btn_view['update']}} wire:click="tosite('leadupdate',{{$user_id}},'UPDATE')">EDIT</button>
                <button class="btnWhite" {{$btn_view['view']}} wire:click="tosite('leadupdate',{{$user_id}},'UPDATE')">VIEW</button>
            </div>
            @endif
        </div>
    @else
        <div class="no-leads pt-5">
            <div class="valign">
                <h1>No Leads Found!</h1>
                <a class="d-block mt-4 border p-2" wire:click="$emit('to_page','leadcreate')">
                    <h5 class="d-inline text-white">Create Lead</h5>
                </a>
            </div>
        </div>
    @endif
</div>