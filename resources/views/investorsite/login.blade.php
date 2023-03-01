<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flow</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/996335939c.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/investorsite.css')}}">
  </head>
  <body class="blck-box text-center">
    <div class="container white-box py-2">
      <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light">
     <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-chevron-left"></i></a>
     </div>
        </nav>
          <div class="login-img mb-2"> <img src="{{asset('logo_blue.png')}}" alt="logo"></div>
    </div>
    <div class="log-container overflow-y-scroll mb-3">
  <div class=" pt-2 pb-3">
    <h1 class="fs-7 fs-34">Log-in</h1>
    <h6 class="sub-head">Invest and get social returns as well</h6>
    </div>
    <div class=" text-center pt-1">
    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row px-3">

                            <div class="col-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} input-field" name="email" value="{{ old('email') }}" placeholder="Email Address"  autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row px-3">

                            <div class="col-12">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row ">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input cust-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-12 ">
                                <button type="submit" class="btn-primary cus-btn btn-create">
                                    {{__('Login') }}
                                </button>
{{--                                @if (Route::has('password.request'))--}}
{{--                                    <a class="btn btn-link" href="{{ route('password.request') }}">--}}
{{--                                        {{ __('Forgot Your Password?') }}--}}
{{--                                    </a>--}}
{{--                                @endif--}}
                            </div>
                        </div>

        <div class="mb-3 text-secondary">
            -------- OR --------
        </div>

                        <div class="form-group row mb-2">
                            <div class='col-12'>
                                <a href='{{ route('login.provider', 'google') }}' class="btn btn-outline-dark google-login" role="button" >
                                    <img width="20px" class="google-logo" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                                  Login with Google
                                </a>
                            </div>
                        </div>


                    </form>
    </div>
  </div>
</div>
  </body>
</html>