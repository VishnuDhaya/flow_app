<div>
<div class="sideBarheaderComheight headerBackground">
    <a class="headerLogout dropdown-toggle logoutRoutewaker" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-sign-out text-white"></i>
    </a>
    <div class="dropdown-menu leadResultstatusbackGround text-center logoutRoute" aria-labelledby="dropdownMenuButton" wire:click="logout">
        <p class="no-margin lightText dropdownText">Logout</p>
    </div>
</div>
    @if (session()->has('delete'))
        <div id = "alert" class="alert alert-success">
            {{ session('delete') }}
        </div>
    @endif
</div>