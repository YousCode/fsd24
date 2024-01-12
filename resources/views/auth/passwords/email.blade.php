@extends('layouts.app', ['isAuth' => false])

@section('content')

        <div class="login-wrapper" data-module="login" id="vue__login">
            <div class="container-fluid h-100">
                <div class="row h-100_notimportant">
                    <div class="col-md-5 password__image no-padding" style="background-image: url({{ url('/images/login-img.png') }});">
                        <div class="login-responsive" style="text-align: center; width:100%;position:absolute;bottom:0;margin-bottom:65px;">
                            <h1 style="font-size: 2.3vw;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                            <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size: 2.3vw;font-weight:bold;">Créez maintenant</p>
                            <p style="font-size: 1.2vw;font-weight:bold;">On s'assure du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-7 pass__form-col" style="display: grid;align-content: center;">
                        <div class="logo-wrapper border-none" style="min-width: 100%;margin-bottom:52px;">
                            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
                            <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
                        </div>
                        <div class="login-form_shape" style="display: flex;">
                            <a href="{{route("login")}}" class="previous-password">
                                <img src="../images/ui/contact-arrow.svg">
                            </a>
                        <form id="resetform" class="login__form no-padding" style="background: rgb(16, 9, 36);min-height: 392px;border-radius: 10px;width: 100%;">
                            @csrf
                            <p class="login__title" style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219));-webkit-background-clip: text;-webkit-text-fill-color: transparent;text-align:left;width: 77%;margin: auto;margin-bottom: 15px;margin-top:0px;padding-top: 43px;">Mot de passe <br/><b style="text-align: right;">oublié ?</b></p>
                            <p style="text-align: left;font-size:12px;width: 77%;margin: auto auto 40px;">Saisissez l’email avec lequel vous avez créé <br/>votre compte Kolab.
                            </p>
                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ __('Votre demande a bien été enregistré.') }}
                                </div>
                            @endif
                            <div class="form-box" style="margin-bottom: 25px;">
                                <input id="email" type="email" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Votre adresse mail') }}" autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                {{-- <span class="form-icon icon-email"></span> --}}
                            </div>
                            {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif --}}
                            <div class="text-center">
                                <button type="submit" id="onclick" class="button is-gradient text-center sending new-button_contact" style="width:77%;padding:15px;text-align:center;font-size: 14px;background: linear-gradient(90deg, #7a3cc9 0%, #c957db 100%)">{{ __('Réinitialiser mon mot de passe') }}</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @section('script')
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script>
                $(document).ready(function (){
                    let start
                    $("input").keyup(function(){
                        clearTimeout(start)
                        let test = document.querySelector("#email").value
                        start = setTimeout(mode,100,test);
                    });
                    $("input").keydown(function(){
                        clearTimeout(start)
                    })
                    let email = document.getElementById("email");

                    function mode(test){
                        $("is-invalid").remove()
                        $("invalid-feedback").remove()
                        $("span").remove()
                        if(test.length > 1) {
                            $.ajax({
                                url: "{{route("checkInput")}}",
                                method: "POST",
                                data: {
                                    email:test,
                                    _token: "{{csrf_token()}}"
                                },
                                success: function (data) {
                                    if(data.success === false) {
                                        if (data.message.email) {
                                            email.classList.add("is-invalid")
                                            $("#email").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.email + '</strong></span>')
                                        }else{
                                            email.classList.remove("is-invalid")
                                        }
                                        $("#onsub").attr("disabled", true)
                                    }else{
                                        Object.keys(data.message).forEach(element=>

                                                email.classList.remove("is-invalid"),
                                            $("span").remove(),
                                            $("invalid-feedback").remove(),
                                            $("field-error").remove(),
                                            $(".error-message").remove(),
                                            $("#onsub").attr("disabled",false)
                                        );

                                    }
                                },
                                error: function (data) {
                                    console.log("Errors")
                                }
                            })
                        }else{
                            email.classList.remove("is-invalid"),
                                $("span").remove()
                            $("invalid-feedback").remove()
                            $("is-invalid").remove()
                            $("field-error").remove()
                            $("#onsub").prop("disabled",false);
                        }
                    }

                    $("#resetform").submit(function(e){
                        const result = document.querySelector("#onclick");
                        let button = document.getElementsByClassName("new-button_contact")
                        e.preventDefault();
                        $.ajax({
                            url: "{{route("password.email")}}",
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType: 'json',
                            data: {
                                email:$("#email").val(),
                            },
                            success:function(data){
                                result.innerHTML = "Votre demande a bien été envoyée par mail";
                                button[0].style.background = 'linear-gradient(103.89deg, #72C93C 35.36%, #55B31C 72.37%), #100824';
                                if(data.message){
                                    setTimeout(()=>
                                    window.location.href = "{{route("login")}}",3000)
                                }
                            },
                            error: function(data){
                                if (data.responseJSON.errors.email) {
                                    email.classList.add("is-invalid")
                                    $("#email").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.responseJSON.errors.email + '</strong></span>')
                                }else{
                                    email.classList.remove("is-invalid")
                                }

                                console.log("error", data);
                            }
                        })
                    });
                })
            </script>
        @stop
    {{-- </div> --}}
{{-- </div> --}}
@endsection



