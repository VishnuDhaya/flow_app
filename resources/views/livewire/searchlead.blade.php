<div class="leadResultcontainer">
    <div class="editLeadtitle">
        <h1 class="no-margin">SEARCH LEAD</h1>
    </div>
    <div class="leadResultcontainerOne">
        <div class="row no-margin leadNamecontainerInsidepad">
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Created from</p>
                <div class="dropdownPosition">
                    <input class="w-100" type="date" id="start" value="2018-05" wire:model="searchfilter.created_from">
                </div>
            </div>
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Created to</p>
                <div class="dropdownPosition">
                    <input class="w-100" type="date" id="start" value="2018-05" wire:model="searchfilter.created_to">
                </div>
            </div>
        </div>
        <div class="row no-margin leadNamecontainerInsidepad">
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Status</p>
                <select class="custom-select w-75 loginInput custom-select-sm no-padding" wire:model="searchfilter.tf_status">
                    <option selected value="">choose</option>
                    @foreach($status as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Account Number</p>
                <input class="w-75 loginInput" wire:model="searchfilter.account_num">
            </div>
        </div>
        <div class="row no-margin leadNamecontainerInsidepad">
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Recruiter ID</p>
                <input class="w-75 loginInput"wire:model="searchfilter.UEZM_MainContent_txtRecruiterID">
            </div>
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Mobile Number</p>
                <input class="w-75 loginInput" wire:model="searchfilter.mobile_num">
            </div>
        </div>
        <div class="row no-margin leadNamecontainerInsidepad">
            <div class="col-md-6 no-padding">
                <p class="no-margin lightText">Biz Name / Cust Name</p>
                <input class="w-75 loginInput"wire:model="searchfilter.biz_num">
            </div>
        </div>
        <div class="text-right leadResultbtnBox">
            <button class="text-white btn-secondary" wire:click="resetVal">Reset</button>
            <button class="text-white" wire:click="search">Search</button>
        </div>
    </div>
</div>