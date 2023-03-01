<div class="container-1280 loginBackground">
    <form wire:submit.prevent="loginUser" class="loginContainer">
        <div class="text-center">
            <img class="loginImg" src="{{asset('img/investorsite/logo_white.png')}}" />
        </div>
        <div>
            <p class="no-margin lightText">Email</p>
            <input wire:model="email" class="w-100" />
            @error('email') <span class="error text-white">{{ $message }}</span> @enderror
        </div>
        <div class="loginInputpad">
            <p class="lightText no-margin">Password</p>
            <input type="password" wire:model="password" class="w-100" />
            @error('password') <span class="error text-white">{{ $message }}</span> @enderror
        </div>
        <div class="text-center">
            <button type='submit' class="loginBtn text-white">LOGIN</button>
            @if (session()->has('error'))
                <div class="alert alert-success">
                    {{ session('error') }}
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </form>
</div>
