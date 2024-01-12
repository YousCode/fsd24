<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        (function() {
            window.sib = {
                equeue: [],
                client_key: "q9sf55pv2lqohhocs71uz29a"
            };
            /* OPTIONAL: email for identify request*/
            //window.sib.email_id = window.email;
            window.sendinblue = {};
            for (var j = ['track', 'identify', 'trackLink', 'page'], i = 0; i < j.length; i++) {
                (function(k) {
                    window.sendinblue[k] = function() {
                        var arg = Array.prototype.slice.call(arguments);
                        (window.sib[k] || function() {
                            var t = {};
                            t[k] = arg;
                            window.sib.equeue.push(t);
                        })(arg[0], arg[1], arg[2], arg[3]);
                    };
                })(j[i]);
            }
            var n = document.createElement("script"),
                i = document.getElementsByTagName("script")[0];
            n.type = "text/javascript", n.id = "sendinblue-js", n.async = !0, n.src = "https://sibautomation.com/sa.js?key=" + window.sib.client_key, i.parentNode.insertBefore(n, i), window.sendinblue.page();
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('/images/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ url('/images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link type="text/css" rel="stylesheet" href="https://unpkg.com/bootstrap/dist/css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>Démo</title>
</head>
<body class="body theme-dark">
    <div class="main-wrapper" style="height: 100%;">
        <div class="login-wrapper contact-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100_notimportant">
                    <div class="col-md-5 no-padding login__image__contact" style="background-image: url({{ url('/images/login-img.png') }});padding:0;background-size: cover;background-position: center;background-repeat: no-repeat;width: 100%;">
                        <div class="login-responsive">
                            <h1 style="font-size: 2.3vw;color: white;font-weight: 700;">Doutez de tout, sauf de vous.</h1>
                            <p style="background: -webkit-linear-gradient(left, rgb(118, 55, 199), rgb(201, 87, 219)); -webkit-background-clip: text;-webkit-text-fill-color: transparent;font-size: 2.3vw;font-weight:bold;">Créez maintenant</p>
                            <p style="font-size: 1.2vw;font-weight:bold;">On s'assure du reste.</p>
                        </div>
                    </div>
                    <div class="col-md-7 demo_form-col" style="display: flex;text-align: center;align-items: center;position: relative;">
                        <div style="width: 100%;height:100%; position: relative;">
                            <div style="width: 100%;height:100%;position: relative;">
                        <div class="demo-contact_wrapper">
                            <div class="logo-wrapper">
                                <div class="logo-wrapper_image" style="min-width: 100%;">

                                    <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="116" height="31.44">
                                    <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark contact-logo" alt="kolab" width="116" height="31.44" style="margin-bottom: 70px;">

                                    <h1 class="demo-title" >Demander<br> une démo</h1>
                                    <p class="to-login" style="margin-bottom: 50px;text-align: center;font-size:14px;">Vous avez déjà un compte?
                                        @if (Route::has('app_logout'))
                                        <a style="color:#C2A6FF;text-decoration:underline;" href="{{ route('app_logout') }}"><strong> Connectez-vous ici !</strong></a>
                                        @endif
                                    </p>

                                </div>
                            </div>

                        </div>


                            <form id="formId" class="form-contact_wrapper">
                                @csrf
                                @honeypot
                                <fieldset class="first_field">

                                    <div class="next-cat arrow next contact-radio" >
                                        <div class="contact-radio_wrapper" >
                                            <div>
                                                <p style="font-weight: 700;">Je suis </p>
                                                <p class="demo" style="width: 100%;position: relative;"> un freelance<br> dans la post-production </p>
                                                <p style="font-size: 1.1rem;color: #46327E;">Par exemple : monteur, étalonneur, motion designer, artiste VFX ou 3D, graphiste, sound designer...</p>
                                            </div>
                                        </div>
                                        <input class="paume" type="radio" name="contact_status" value="{{\App\ContactRoleEnum::freelance}}" style="width: 100%;position: absolute;height: 100%;">
                                    </div>

                                    <div class="next-cat arrow next contact-radio" >
                                        <div class="contact-radio_wrapper" >
                                            <div>
                                                <p style="font-weight: 700;">Je suis</p>
                                                <p class="demo" style="width: 100%;position: relative;">un post-producteur indépendant</p>
                                            </div>

                                        </div>
                                        <input type="radio" class="paume" name="contact_status" value="{{\App\ContactRoleEnum::producer}}"  style="width: 100%;position: absolute;height: 100%;">
                                    </div>
                                    <div class="next-cat arrow next contact-radio" >
                                        <div class="contact-radio_wrapper">
                                            <div>
                                                <p style="font-weight: 700;">Je suis </p>
                                                <p class="demo" style="width: 100%;position: relative;">une société</p>
                                            </div>
                                        </div>
                                        <input type="radio" class="paume" name="contact_status" value="{{\App\ContactRoleEnum::business}}" style="width: 100%;position: absolute;height: 100%;">
                                    </div>
                                </fieldset>
                                <fieldset style="margin-bottom: 7rem;">
                                    <div style="display: flex; align-items: center;">
                                        <div class="login__form contact-login__form" style="width: 60%;margin: auto;padding: 1.2vw 0 3vw;background:#100924;border-radius:10px;" >
                                            @csrf
                                            <p class="form-title" style="margin-bottom: 25px;">Renseignez vos coordonnées,<br/>
                                                pour que nous puissions vous contacter dès que possible.
                                            </p>

                                            <div class="form-box formbottom">
                                                <div class="justify-error__field">
                                                    <input id="workspace" type="text" class="form-field @error('workspace') is-invalid field-error @enderror" name="workspace"  value="{{ old('workspace') }}" placeholder="{{ __('Espace de travail*') }}"  autocomplete="workspace" autofocus>
                                                    @error('workspace')
                                                        <span class="invalid-feedback error-message" role="alert">
                                                            <strong class="red-error-message">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-box formbottom">
                                                <div class="justify-error__field">
                                                    <input id="lastname" type="text" class="form-field @error('lastname') is-invalid field-error @enderror" name="lastname"  value="{{ old('lastname') }}" placeholder="{{ __('Nom*') }}"  autocomplete="lastname" autofocus>
                                                        @error('lastname')
                                                            <span class="invalid-feedback error-message" role="alert">
                                                                <strong class="red-error-message">{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="form-box formbottom">
                                                <div class="justify-error__field">
                                                    <input id="firstname" type="text" class="form-field @error('firstname') is-invalid field-error @enderror" name="firstname" value="{{ old('firstname') }}" placeholder="{{ __('Prénom*') }}"  autocomplete="firstname" autofocus>
                                                    @error('firstname')
                                                        <span class="invalid-feedback error-message" role="alert">
                                                            <strong class="red-error-message">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-box formbottom">
                                                <div class="justify-error__field">
                                                    <input id="email" type="email" class="form-field @error('email') is-invalid field-error @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Adresse Mail*') }}"  autocomplete="email" >
                                                    @error('email')
                                                        <span class="invalid-feedback error-message" role="alert">
                                                            <strong class="red-error-message">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-box formbottom">
                                                <div class="justify-error__field">
                                                    <input id="phone" type="tel" name="phone" class="form-field  @error('phone') is-invalid field-error @enderror" value="{{ old('phone') }}" placeholder="{{ __('Numéro de téléphone') }}"  >
                                                    @error('phone')
                                                    <span class="invalid-feedback error-message" role="alert">
                                                        <strong class="red-error-message">{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div>
                                                <button type="submit" id="submitfrm" class="button is-gradient new-button_contact text-center" style="padding: 15px;background: linear-gradient(90deg, #7a3cc9 0%, #c957db 100%)"><b style="padding-left: 7px;">{{ __('Demander une démo') }}</b></button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="previous">
                                        <img src="../images/ui/contact-arrow.svg">
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                            <div class="step_contact">
                                <div style="width: 100%;justify-content: center;margin: auto;display: flex;gap: 1rem;">
                                    <div class="step-slider active" ></div>
                                    <div class="step-slider" ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
    <!-- jQuery easing plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" type="text/javascript"></script>
    <script>

    let current;
    let next,previous;
    let left,right, opacity, scale;
    let animating;

    $(".next-cat").click( function() {

        if(animating)
            return false;

        animating = true;
        current = $(this).parent();
        next = $(this).parent().next();

        $(".step-slider").eq($(".login__form").index(next)).addClass("active");

        next.show();

        current.animate({opacity: 0}, {
            step: function(now, mx) {
                scale = 1 - (0.9 - now) * 0.1;

                left = (now * 60)+"%";
                right = (now * 60) - "%";

                opacity = 1 - now;
                current.css({ 'right':right,position:'absolute'});
                next.css({'left': left, 'opacity': opacity,transform: "scale(" + scale + ")",position:'relative'});
            },
            duration: 800,


            complete: function(){
                current.hide();
                animating = false;
            },
            easing: 'easeOutQuint'
        });
    });

    $(".previous").click(function(){

        if(animating)
            return false;
        animating = true;
        current= $(this).parent();
        previous = $(this).parent().prev();


	//de-activate current step on progressbar
	$(".step-slider").eq($(".login__form").index(current)).removeClass("active");

	//show the previous fieldset
	previous.show();
	//hide the current fieldset with style
	current.animate({opacity: 0}, {
		step: function(now, mx) {
			left = ((1-now) * 50)+"%";
			opacity = 1 - now;
			current.css({'left': left,position:"absolute"});
			previous.css({ 'opacity': opacity,position:"relative"});
		},
		duration: 800,
		complete: function(){
			current.hide();
			animating = false;
		},
		easing: 'easeInOutQuint'
	});
    });
    </script>
    <script>
        $(document).ready(function(){
            let start
            $("input").keyup(function(){
                clearTimeout(start)
                let test = document.querySelector("#email").value
                let phone = document.querySelector("#phone").value;
                let last = document.querySelector("#lastname").value;
                let first = document.querySelector("#firstname").value;
                let work = document.querySelector("#workspace").value;

                start = setTimeout(mode,500,test,last,first,phone,work);
            });
            $("input").keydown(function(){
                clearTimeout(start)
            })
            let email = document.getElementById("email");
            let lastname = document.getElementById("lastname");
            let firstname = document.getElementById("firstname");
            let workspace = document.getElementById("workspace");

            function mode(test,last,first,phone,work){
                $("span").remove()
                $("invalid-feedback").remove()
                if(test.length > 1 || last.length > 1 || first.length > 1 || phone.length > 1) {
                    $.ajax({
                        url: "{{route("check")}}",
                        method: "POST",
                        data: {
                            workspace:work,
                            email:test,
                            firstname:first,
                            lastname:last,
                            phone:phone,
                            _token: "{{csrf_token()}}"
                        },
                        success: function (data) {
                            if(data.success === false) {
                                let errors = document.querySelector(".error-message")
                                let elementor = data.message
                                const onlyInputs = document.querySelectorAll('#demo-form input');

                                if (data.message.workspace) {
                                    workspace.classList.add("is-invalid")
                                    $("#workspace").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.workspace + '</strong></span>')
                                } else {
                                    workspace.classList.remove("is-invalid")
                                }

                                let phone = document.getElementById("phone");
                                if (data.message.email) {
                                    email.classList.add("is-invalid")
                                    $("#email").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.email + '</strong></span>')
                                } else {
                                    email.classList.remove("is-invalid")
                                }

                                if (data.message.lastname) {
                                    lastname.classList.add("is-invalid")
                                    $("#lastname").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.lastname + '</strong></span>')
                                } else {
                                    lastname.classList.remove("is-invalid")
                                }

                                if(data.message.firstname){
                                    firstname.classList.add("is-invalid")
                                    $("#firstname").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.firstname + '</strong></span>')
                                } else {
                                    firstname.classList.remove("is-invalid")
                                }

                                if(data.message.phone){
                                    phone.classList.add("is-invalid")
                                    if(data.message.phone.length < 2)
                                        $("#phone").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.phone[0]+ '</strong></span>')
                                    if(data.message.phone.length > 1){
                                        $("#phone").after('<span class="invalid-feedback error-message" role="alert"> <strong class="red-error-message">' + data.message.phone[1]+ '</strong></span>')
                                    }
                                } else {
                                    phone.classList.remove("is-invalid")
                                }

                                $("#submitfrm").attr("disabled", true)
                            }else{
                                let phone = document.getElementById("phone");
                                Object.keys(data.message).forEach(element=>

                                    workspace.classList.remove("is-invalid"),
                                    email.classList.remove("is-invalid"),
                                    lastname.classList.remove("is-invalid"),
                                    firstname.classList.remove("is-invalid"),
                                    phone.classList.remove("is-invalid"),
                                    $("span").remove(),
                                    $("invalid-feedback").remove(),
                                    $("field-error").remove(),
                                    $("#submitfrm").attr("disabled",false)
                                );
                            }
                        },
                        error: function (data) {
                            console.log(data)
                        }
                    })
                } else {
                    email.classList.remove("is-invalid"),
                    $("span").remove()
                    $("invalid-feedback").remove()
                    $("is-invalid").remove()
                    $("field-error").remove()
                    $("#submitfrm").prop("disabled",false);
                }
            }

            $("#formId").submit(function(e){
                const animation = document.querySelector("#submitfrm");
                let button = document.getElementsByClassName("new-button_contact")
                const svg = '<svg width="14" height="11" style="margin-right: 6px" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.15572 10.2953C5.80067 10.6769 5.19708 10.679 4.8394 10.2999L0.594738 5.8014C0.262496 5.44929 0.267479 4.89769 0.606028 4.55164L0.886902 4.26454C1.24344 3.9001 1.83095 3.90339 2.18339 4.2718L5.47029 7.70769L12.0681 0.666187C12.4116 0.299572 12.9885 0.284295 13.3509 0.632214L13.6593 0.928301C14.0148 1.26949 14.0306 1.83275 13.695 2.19342L6.15572 10.2953Z" fill="white"/> </svg>'
                e.preventDefault();
                $.ajax({
                    url: "{{route("contact")}}",
                    method: "POST",
                    dataType: 'json',
                    data: {
                        contact_status: $("input[name='contact_status']:checked").val(),
                        workspace:$("#workspace").val(),
                        email:$("#email").val(),
                        firstname:$("#firstname").val(),
                        lastname:$("#lastname").val(),
                        phone:$("#phone").val(),
                        _token: "{{csrf_token()}}",
                    },
                    success:function(data){
                        animation.innerHTML = svg +"    " +"   Demande envoyée. Vous recevrez un email lorsqu’elle sera validée";
                        button[0].style.background = 'linear-gradient(103.89deg, #72C93C 35.36%, #55B31C 72.37%), #100824';
                        if(data.success){
                            setTimeout(()=>
                            window.location.href = "{{route("login")}}"
                            ,12000)
                        }
                    },
                    error: function(data){
                        return data.error.message
                        console.log("error", data);
                    }
                })
            });
        });
    </script>


</body>
</html>
