@php
$inv_serv = new \App\Services\InvApp\InvAppService();
$inv_person_id = auth('investors')->user()->person_id;
$bond = $inv_serv->best_bond($inv_person_id)
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8">
      <meta name="theme-color" content="#DCF0FF">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="Content-Language" content="en">
      <meta name="google" content="notranslate">
    <title>Flow Investor</title>
    <link rel="icon" href="{{asset('icon.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;900&display=swap" rel="stylesheet">
      <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/996335939c.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ asset('css/investorsite.css')}}">
    
  </head>
  <body class="blck-box home-box">
@yield('head')
<div class="container custom-navbar-container myAsset px-0">
    <nav class="navbar  navbar-light">
        <div class="container-fluid justify-content-between px-0">
            <button class="navbar-toggler cust-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="m-0">
                <h3 class="navbar-brand mb-0 pb-0"><b> @yield('brand') </b></h3>
            </div>
            <div>
            </div>
            <div class="collapse navbar-collapse cust-navbar-collapse text-start" id="navbarNavAltMarkup">
                <div class="navbar-nav bg-white p-2 rounded">
                    <a class="nav-link active fs-5 pb-1" aria-current="page" href="/inv/home">Home</a>
                    <a class="nav-link fs-5 pb-1" aria-current="" href="#">Profile</a>
                    <a class="nav-link fs-5 fw-bold pb-1" aria-current="" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;" >
    @csrf
</form>
@yield('body')
<div class="bottom-bar py-2">
    <nav class="d-flex justify-content-around">
        <div class="footer-item"><a class="navbar-item nav-link {{Request::path() === 'inv/home' ? 'active' : ''}}" href="/inv/home">
                <ul class="navbar-nav text-center">
                    <li><i class="fas fa-home"></i></li>
                    <li>Home</li></ul></a></div>
        <div class="footer-item">
            <a class="navbar-item nav-link {{preg_match("#inv/bonds/#",Request::path())? 'active' : Request::path()}}" href="/inv/bonds/{{$bond->fund_code}}"><ul class="navbar-nav text-center">
                    <li><i class="fas fa-search"></i></li>
                    <li>Details</li>
                </ul></a></div>
        <div class="footer-item">
            <a class="navbar-item nav-link {{Request::path() === 'inv/transactions' ? 'active' : ''}}" href='/inv/transactions'><ul class="navbar-nav text-center">
                    <li><i class="fas fa-funnel-dollar"></i></li>
                    <li>Transactions</li>
                </ul></a></div>
        <div class="footer-item">
            <a class="navbar-item nav-link {{Request::path() === 'inv/bank_acc' ? 'active' : ''}}" href="/inv/bank_acc" ><ul class="navbar-nav text-center">
                    <li><i class="fas fa-user"></i></li>
                    <li>Bank A/C</li>
                </ul></a></div>
    </nav>
</div>
</div>
<script src="{{ asset('js/inv.js')}}"></script>
</body>
</html>