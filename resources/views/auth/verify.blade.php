
@extends('layouts.app', ['isAuth' => true])

@section('content')

        <div class="login-wrapper" data-module="login" id="vue__login">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-5 login__image no-padding" style="background-image: url({{ url('/images/login-img.png') }});">
                        <div class="login-responsive" style="text-align: center; width:100%;position:absolute;bottom:0;margin-bottom:65px;">
                            <h1 style="font-size: 25.7px;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                            <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size:25.7px;font-weight:bold;">Créez maintenant</p>
                            <p style="font-size: 9.8px;font-weight:bold;">On s'occupe du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-7 login__form-col" style="padding-bottom: 50px;">
                        <div class="logo-wrapper" style="min-width: 100%;position: relative;top: -102px;display: flex;align-items: center;justify-content: center;">
                            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
                            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
                        </div>
                        <p class="login__title" style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219));-webkit-background-clip: text;-webkit-text-fill-color: transparent;text-align:center;">Hello !</p>
                        <p style="margin-bottom: 69px;text-align: center;font-size:10px;">Déjà un compte?
                            @if (Route::has('app_logout'))
                            <a style="color:#C2A6FF;text-decoration:underline;" href="{{ route('app_logout') }}"> Connectez-vous ici !</a>
                            @endif
                        </p>
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                        </form>
                    </div>
                    {{-- <div class="col-md-5 login__image" style="background-image: url({{ url('/images/login-img.png') }});"></div>  --}}
                </div>
            </div>
        </div>

    {{-- </div> --}}
{{-- </div> --}}
@endsection
