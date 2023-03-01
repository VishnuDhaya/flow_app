<!-- side bar start -->
<div class="col-lg-2 d-none d-lg-block no-padding sideBarcontainer">
    <img class="loginImg ml-5 py-1 w-50" src="{{asset('img/investorsite/logo_white.png')}}" />
    <h3 class="pl-3 no-margin sideBaruserName text-uppercase"><span class = "rounded-circle bg-warning text-uppercase p-2">{{$acronym}}</span>  {{$username}}<span class="role"> ({{$role}})</span></h3>
    <div class="nav-first-link-margin-top">
        <a class="nav-first-link-decoration" wire:click="to_page('leadhome')">
            <div class="dropdown-box-toggle">
                <i class="fa fa-home fa-pad-left"></i>
                <p class="nav-display-flex no-margin">Home</p>
            </div>
        </a>
    </div>
    <div class="dropdown-box-container">
        <div class="dropdown-box-toggle-no-hover dropdown-box-toggleLeads">
            <i class="fa fa-users fa-pad-left"></i>
            <p class="nav-display-flex no-margin">Leads</p>
        </div>
        <div class="dropdown-box">
            <a class="dropdown-box-item d-block" wire:click="to_page('leadcreate')">
                <i class="fa fa-location-arrow fa-rotate-45"></i>
                <label class="dropdown-box-item-pad_left">Create</label>
            </a>
            <a class="dropdown-box-item d-block" wire:click="to_page('leadsearch')">
                <i class="fa fa-location-arrow fa-rotate-45"></i>
                <label class="dropdown-box-item-pad_left">Search</label>
            </a>
        </div>
    </div>
</div>
<!-- only shown in medium device part start -->
<div class="col-md-1 d-none d-md-block d-lg-none no-padding sideBarcontainer">
    <!-- <h1 class="text-center no-margin sideBarheaderComheight">FLOW</h1> -->
    <div class="sideBarheaderComheight flowRoundiconBox">
        <img src="images/flowroundlogo.png"/>
    </div>
    <h3 class="text-center no-margin sideBaruserName userNamerefont"><span class = "rounded-circle text-uppercase text-white bg-warning p-2">{{$acronym}}</span></h3>
    <div class="nav-first-link-margin-top">
        <a class="nav-first-link-decoration" href="lead.html">
            <div class="dropdown-box-toggle homeIconrefont">
                <i class="fa fa-home fa-pad-left"></i>
            </div>
        </a>
    </div>
    <div class="dropdown-box-container">
        <div class="dropdown-box-toggle dropdown-box-toggleLeads">
            <i class="fa fa-users fa-pad-left"></i>
            <div class="navDropdownIndicator">
                <i class="fa fa-caret-down navDropdownIndicatordown"></i>
            </div>
        </div>
        <div class="dropdown-box dropdown-boxShow">
            <a class="dropdown-box-item d-block sideBarfnIcon" wire:click="">
                <i class="fa fa-search sideBarfnIconpad"></i>
                <label>S</label>
            </a>
            <a class="dropdown-box-item d-block sideBarfnIcon" wire:click="">
                <i class="fa fa-plus sideBarfnIconpad"></i>
                <label>C</label>
            </a>
        </div>
    </div>
</div>
<!-- only shown in medium device part end -->
<!-- side bar end -->