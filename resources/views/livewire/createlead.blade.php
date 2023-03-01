<div class="leadCreatecontainer">
    @if (session()->has('status'))
        <div id="alert" class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="createLeadtitle">
        <h1 class="no-margin">CREATE LEAD</h1>
    </div>
            <div class="ifScrollablecreateLead">
                <div class="row no-margin">
                    <div class="col-md-1 no-padding statusBorder">
                        <div class="statusBox row">
                            <div class="col-10 pr-1">
                                <p class="statusText no-margin" class="no-margin">Biz Info</p>
                            </div>
                            <div class="col-1">
                                <img class="statusImg" src="{{asset("img/lead/crnt.png")}}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-11 pl-4">
                        <h3 class="createLeadsubTitle no-margin createLeadsubTitlenoPadtop">Biz Info</h3>
                        <div class="leadCreatecontainerOne">
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Business Name / Cust Name</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.biz_name"><br/>
                                    @error('leadDataArr.biz_name') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Mobile Number</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.mobile_num"><br/>
                                    @error('leadDataArr.mobile_num') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">National ID</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.national_id"><br/>
                                    @error('leadDataArr.national_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-1 no-padding statusBorder">
                        <div class="statusBox row">
                            <div class="col-10 pr-1">
                                <p class="statusText no-margin" class="no-margin">EzeeMoney KYC</p>
                            </div>
                            <div class="col-1">
                                <img class="statusImg" src="{{asset("img/lead/crnt.png")}}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-11 pl-4">
                        <h3 class="createLeadsubTitle no-margin">EzeeMoney KYC</h3>
                        <div class="leadCreatecontainerOne">
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Abbrevation Name</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.UEZM_MainContent_txtAbbreviationName">
                                </div>
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Company Registration No</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.UEZM_MainContent_txtCompanyRegistrationNo">
                                </div>
                            </div>
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Recruiter ID</p>
                                    <input class="w-75 loginInput" wire:model="leadDataArr.UEZM_MainContent_txtRecruiterID">
                                </div>
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Nature Of Business</p>
                                    <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="leadDataArr.UEZM_MainContent_ddlNatureOfBusiness">
                                        <option selected value="">choose</option>
                                        @foreach($natureDrop as $nature)
                                            <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Operated By</p>
                                    <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="leadDataArr.UEZM_MainContent_ddOperatedBy">
                                        <option selected value="">choose</option>
                                        @foreach($opertedbyDrop as $nature)
                                            <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Wallet Type</p>
                                    <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="leadDataArr.UEZM_MainContent_ddWallet">
                                        <option selected value="">choose</option>
                                        <option value=1>sc</option>
                                        <option value=2>Merchant Acct</option>
                                        <option value=3>SC+Merchant Acct</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Zone</p>
                                    <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="leadDataArr.UEZM_MainContent_ddlZone">
                                        <option selected value="">choose</option>
                                        @foreach($zoneDrop as $nature)
                                            <option value={{$nature->data_code}} > {{$nature->data_value}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-1 no-padding statusBorder">
                        <div class="statusBox row">
                            <div class="col-10 pr-1">
                                <p class="statusText no-margin" class="no-margin">Product</p>
                            </div>
                            <div class="col-1">
                                <img class="statusImg" src='{{asset("img/lead/crnt.png")}}' />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-11 pl-4">
                        <h3 class="createLeadsubTitle no-margin">Product</h3>
                        <div class="leadCreatecontainerOne">
                            <div class="row no-margin leadNamecontainerInsidepad">
                                <div class="col-md-6 no-padding">
                                    <p class="no-margin lightText">Product</p>
                                    <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="leadDataArr.product">
                                        <option selected value="">choose</option>
                                        {!! $productDrop !!}
                                    </select></br>
                                    @error('leadDataArr.product') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="leadCreatebuttonBox">
        <button class="btnGreen" wire:click="create_lead">CREATE</button>
    </div>
</div>
