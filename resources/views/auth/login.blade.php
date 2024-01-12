@extends('layouts.app', ['isAuth' => false]) @section('content') {{--
<div class="login" data-module="login" id="vue__login"> --}} {{--
    <div class="main-container"> --}} {{--
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
        </div> --}}

        <div class="login-wrapper" data-module="login" id="vue__login">
            <div class="container-fluid h-100">
                <div class="row h-100_notimportant">
                    <div class="new_login-responsive @if($fromAdmin) admin @endif">
                        <div class="" style="place-self: center;">
                            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="116" height="31.44">
                            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="116" height="31.44">
                        </div>
                        <div class="new_login-responsive-content" style="place-self: center;text-align: center">
                            <div>
                                <img class="login-responsive_image" src="{{url('../images/responsivelogo.png')}}">
                            </div>
                            <div class="p-tag_stronger">
                                <p><strong>Pour vous connecter,</strong></p>
                                <p><strong>rendez-vous</strong></p>
                                <p><strong>sur votre ordinateur</strong></p>
                            </div>
                            <div class="p-tag_normal">
                                <p>Kolab sera bientôt disponible</p>
                                <p>sur mobile</p>
                            </div>
                        </div>
                        <div class="login-responsive_footer" style="text-align: center; width:100%;bottom:0;margin-bottom:65px;place-self: flex-end;margin-bottom: 65px;" >
                            <h1>Doutez de tout, sauf de vous.</h1>
                            <p class="color_p-tag">Créez maintenant</p>
                            <p class="p-content">On s'assure du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-5 login__image no-padding" style="background-image: url({{ url('/images/login-img.png') }});">
                        <div class="login-responsive" style="text-align: center; width:100%;position:absolute;bottom:0;margin-bottom:65px;" >
                            <h1 style="font-size: 2.3vw;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                            <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size: 2.3vw;font-weight:bold;">Créez maintenant</p>
                            <p style="font-size: 1.2vw;font-weight:bold;">On s'assure du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-7 login__form-col @if($fromAdmin) admin @endif" style="display: grid;align-content: center;">
                        <div class="logo-wrapper border-none" style="min-width: 100%;margin-bottom: 52px;">
                            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="116" height="31.44">
                            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="116" height="31.44">
                        </div>
                        <div class="login-form_shape">

                            <form method="POST" action="{{ route('login') }}" class="login__form js-login-form" style="background: #100924;border-radius: 10px;padding-bottom: 31px;">
                                @csrf

                                <p class="login__title" style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219));-webkit-background-clip: text;-webkit-text-fill-color: transparent;text-align:center;padding-top: 30px;">Hello !</p>
                                <p class="login_logout" style="margin-bottom: 35px;text-align: center;font-size:14px;">Vous n'avez pas encore de compte ?<br /> @if (Route::has('app_logout'))
                                        <a style="color:#C2A6FF;text-decoration:underline;" href="{{ route('contact') }}"><strong> Rendez-vous sur ce formulaire.</strong></a> @endif
                                </p>

                                <div class="form-box formbottom">
                                    <input id="email" type="email" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Saisissez votre adresse mail') }}"  autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror {{-- <span class="form-icon icon-email"></span> --}}
                                </div>
                                <div class="form-box formbottom">
                                    <input id="password" type="password" class="form-field @error('password') is-invalid field-error @enderror" name="password" value="{{ $password ?? old('password')}}" placeholder="{{ __('Saisissez votre mot de passe') }}"  autocomplete="current-password">
                                    @error('password')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror {{-- <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span> --}} {{-- <span class="form-icon icon-see see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                                    --}}
                                </div>
                                <div class=" form-box text-right formbottom form_login">
                                    <button type="submit" class="button is-gradient login-arrow new-button re-log" style="width: 77%;" >{{ __('Connexion vers kolab') }}</button>
                                </div>

                                @if (Route::has('password.request'))
                                <div class="text-center">
                                    <a class="forgot-pwd-link" href="{{ route('password.request') }}">
                                            {{ __('Mot de passe oublié ?') }}
                                        </a>
                                </div>
                                @endif

                            </form>
                        </div>
                    </div>
                    {{--
                    <div class="col-md-5 login__image" style="background-image: url({{ url('/images/login-img.png') }});"></div> --}}
                </div>
            </div>
        </div>
        {{-- </div> --}} {{-- </div> --}} @endsection
