{{-- @extends('layouts.app', ['isAuth' => false])

@section('content')
<div class="login" data-module="login">
    <div class="main-container">
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-light" alt="kolab" width="173" height="50">
            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-dark" alt="kolab" width="173" height="50">
        </div>

        <div class="login-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-7 login__form-col">
                        <p class="login__title">Changer votre mot de passe</p>
                        <form method="POST" action="{{ route('password.update') }}" class="login__form">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon email-icon"></span>
                            </div>

                            <div class="form-box">
                                <input id="password" type="password" class="form-field js-password-field @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Nouveau mot de passe') }}" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>

                            <div class="form-box">
                                <input id="password-confirm" type="password" class="form-field js-password-field" name="password_confirmation" placeholder="{{ __('Confirmation mot de passe') }}" required autocomplete="new-password">

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="main-button">{{ __('Réinitialiser le mot de passe') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 login__image" style="background-image: url({{ url('/images/kolab-login.jpg') }});"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app', ['isAuth' => false])

@section('content')

{{-- <div class="login" data-module="login" id="vue__login"> --}}
    {{-- <div class="main-container"> --}}
        {{-- <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
        </div>

        <div class="login-wrapper" data-module="login" id="vue__login">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-5 login__image no-padding" style="background-image: url({{ url('/images/login-img.png') }});">
                        <div class="login-responsive" style="text-align: center; width:100%;position:absolute;bottom:0;margin-bottom:65px;">
                            <h1 style="font-size: 25.7px;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                            <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size:25.7px;font-weight:bold;">Créez maintenant</p>
                            <p style="font-size: 9.8px;font-weight:bold;">On s'assure du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-7 login__form-col">
                        <div class="logo-wrapper" style="min-width: 100%;position: relative;top: -130px;display: flex;align-items: center;justify-content: center;">
                            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
                            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
                        </div>
                        <p class="login__title" style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219));-webkit-background-clip: text;-webkit-text-fill-color: transparent;text-align:center;">Hello !</p>
                        <p style="margin-bottom: 49px;text-align: center;font-size:10px;">Vous n'avez pas encore de compte?
                            @if (Route::has('app_logout'))
                            <a style="color:#C2A6FF;text-decoration:underline;" href="{{ route('register') }}"> Demandez-nous une démo</a>
                            @endif
                        </p>
                        <form method="POST" action="{{ route('password.update') }}" class="login__form">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                {{-- <span class="form-icon email-icon"></span>
                            </div>

                            <div class="form-box">
                                <input id="password" type="password" class="form-field js-password-field @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Nouveau mot de passe') }}" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>

                            <div class="form-box">
                                <input id="password-confirm" type="password" class="form-field js-password-field" name="password_confirmation" placeholder="{{ __('Confirmation mot de passe') }}" required autocomplete="new-password">

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>
                            {{-- @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="text-right" id="onclick">
                                <button type="submit" class="main-button ">{{ __('Réinitialiser le mot de passe') }}</button>
                            </div>
                        </form>
                    </div>
                    {{-- <div class="col-md-5 login__image" style="background-image: url({{ url('/images/login-img.png') }});"></div>
                </div>
            </div>
        </div>

    {{-- </div>
{{-- </div>

@endsection --}}

@extends('layouts.app', ['isAuth' => false])

@section('content')

    <div class="login-wrapper" data-module="login" id="vue__login">
        <div class="container-fluid h-100">
            <div class="row h-100_notimportant">
                <div class="col-md-5 password__image no-padding" style="background-image: url({{ url('/images/login-img.png') }});">
                    <div class="login-responsive" style="text-align: center; width:100%;position:absolute;bottom:0;margin-bottom:65px;">
                        <h1 style="font-size: 2.3vw;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                        <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size:2.3vw;font-weight:bold;">Créez maintenant</p>
                        <p style="font-size: 1.2vw;font-weight:bold;">On s'assure du reste.</p>
                    </div>
                </div>
                <div class="col-md-7 pass__form-col" style="display: grid;align-content: center;">
                    <div class="logo-wrapper border-none" style="margin-bottom:52px;width: 300px;margin: auto;padding-bottom: 57.56px;">
                        <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
                        <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
                    </div>
                    <div class="login-form_shape" style="background-color: rgb(16, 9, 36);max-width: 529px;padding-top: 66px;">
                        <p class="login__title" style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219));-webkit-background-clip: text;-webkit-text-fill-color: transparent;text-align:left;width: 77%;margin: auto;margin-bottom: 15px;">Choisissez votre <br/><b style="text-align: right;">nouveau mot de passe</b></p>
                        <p style="text-align: left;font-size:14px;width: 77%;margin: auto;margin-bottom:40px;">Utilisez au moins 8 caractères avec des lettres dont <br/>au minimum 1 majuscule, 1 chiffre et 1 symbole.
                        </p>
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ __('Votre demande a bien été enregistré.') }}
                            </div>
                        @endif
                        <form id="passform" class="reset__form height-auto">
                            @csrf
                            <input id="token"  type="hidden" name="token" value="{{ $token }}">


                           {{-- <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                {{-- <span class="form-icon email-icon"></span>
                            </div>--}}

                            <input id="email" type="hidden" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Votre adresse mail') }}" autocomplete="email" autofocus>

                            <div class="form-box" style="margin-bottom: 25px;">
                                <input id="password" type="password" class="form-field @error('password') is-invalid field-error @enderror" name="password" placeholder="{{ __('Choisissez un nouveau mot de passe') }}" autocomplete="new-password">
                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>

                            <div class="form-box" style="margin-bottom: 25px;">
                                <input id="password-confirm" type="password" class="form-field @error('password') is-invalid field-error @enderror" name="password_confirmation" placeholder="{{ __('Confirmez votre nouveau mot de passe') }}" autocomplete="new-password">
                                @error('password')
                                <span class="invalid-feedback error-message" role="alert" style="margin-top: 32px;">{{ $message }}</span>
                                @enderror
                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>
                            {{-- @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif --}}
                            <div class="text-center">
                                <button type="submit" id="onsub" class="button is-gradient text-center sending new-button" style="width:77%;padding:15px;text-align:center;margin-bottom: 60px;font-size: 14px;">{{ __('Valider mon nouveau mot de passe') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- <div class="col-md-5 login__image" style="background-image: url({{ url('/images/login-img.png') }});"></div>  --}}
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(document).ready(function (){
            let start
            $("input").keyup(function(){
                clearTimeout(start)
                let passwords = document.querySelector("#password").value
                let confirmation = document.querySelector("#password-confirm").value
                let token = document.querySelector("#token").value
                let email = document.querySelector("#email").value

                start = setTimeout(mode,100,passwords,confirmation,token,email);
            });
            $("input").keydown(function(){
                clearTimeout(start)
            })

            let password = document.getElementById("password");
            let confirmation = document.getElementById("password-confirm")
            function mode(passwords,confirmations,token){
                $("is-invalid").remove()
                $("invalid-feedback").remove()
                $("span").remove()
                if(passwords.length > 1 || confirmation.length > 1) {
                    $.ajax({
                        url: "{{route("checkIt")}}",
                        method: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            token:token,
                            password:passwords,
                            password_confirmation: confirmations,

                        },
                        success: function (data) {
                            if(data.success === false) {
                                if (data.message.password) {
                                    if(passwords.length < 10 && confirmations.length <1) {
                                        password.classList.add("is-invalid")
                                        confirmation.classList.add("is-invalid")
                                        $("#password").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.password[1] + '</strong></span>')
                                    }else{
                                        if(passwords !== confirmation){
                                            password.classList.add("is-invalid")
                                            confirmation.classList.add("is-invalid")
                                            $("#password").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.password[0] + '</strong></span>')
                                        }
                                    }
                                }else{
                                    password.classList.remove("is-invalid")
                                    confirmation.classList.remove("is-invalid")
                                }
                                $("#onsub").attr("disabled", true)
                            }else{
                                let phone = document.getElementById("phone");
                                Object.keys(data.message).forEach(element=>
                                        confirmation.classList.remove("is-invalid"),
                                        password.classList.remove("is-invalid"),
                                    $("span").remove(),
                                    $("invalid-feedback").remove(),
                                    $("field-error").remove(),
                                    $(".error-message").remove(),
                                    $("#onsub").attr("disabled",false)
                                );

                            }
                        },
                        error: function (data) {
                            console.log(data.message)
                            $("#password").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.password[0] + '</strong></span>')
                            console.log(data.error.message)
                        }
                    })
                }else{
                    password.classList.remove("is-invalid"),
                        confirmation.classList.remove("is-invalid"),
                        $("span").remove()
                    $("invalid-feedback").remove()
                    $("is-invalid").remove()
                    $("field-error").remove()
                    $("#onsub").prop("disabled",false);

                }
            }
            $("#passform").submit(function(e){
                const result = document.querySelector("#onsub");
                const svg = '<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.15572 10.2953C5.80067 10.6769 5.19708 10.679 4.8394 10.2999L0.594738 5.8014C0.262496 5.44929 0.267479 4.89769 0.606028 4.55164L0.886902 4.26454C1.24344 3.9001 1.83095 3.90339 2.18339 4.2718L5.47029 7.70769L12.0681 0.666187C12.4116 0.299572 12.9885 0.284295 13.3509 0.632214L13.6593 0.928301C14.0148 1.26949 14.0306 1.83275 13.695 2.19342L6.15572 10.2953Z" fill="white"/> </svg>'
                e.preventDefault();
                $.ajax({
                    url: "{{route("password.update")}}",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    data: {
                        token:$("#token").val(),
                        _token: "{{csrf_token()}}",
                        email:$("#email").val(),
                        password:$("#password").val(),
                        password_confirmation: $("#password-confirm").val(),
                    },
                    success:function(data){
                        result.innerHTML ="Votre mot de passe a bien été réinitialisé";
                        window.location.href = "{{route("login")}}"

                    },
                    error: function(data){
                        console.log(data.responseText)
                        $("#password").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.errors.password[0] + '</strong></span>')
                    }
                })
            });
        })
    </script>
    @stop
    {{-- </div> --}}
    {{-- </div> --}}
@endsection



