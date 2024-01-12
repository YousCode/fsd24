@php setlocale(LC_TIME, 'fr_FR') @endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{ url()->full() }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- Metas -->
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('meta_description')" />

    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <!-- <link type="text/css" rel="stylesheet" href="https://unpkg.com/bootstrap/dist/css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css" /> -->

    <!-- Load polyfills to support older browsers -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver" crossorigin="anonymous"></script>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('/images/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ url('/images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">

    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="body theme-dark @yield('bodyClass')" data-mode="{{ config('app.env') }}" data-anim="wrapper" data-barba="wrapper">
    <div class="main-wrapper"  data-barba="container" data-anim="container" id="app" style="height:100%;">
        <main class="js-scroll-container" data-barba="container" data-barba-namespace="home-section" style="height:100%;">
            @if($isAuth || (isset($isShared) && $isShared))
                @section('partials.header')
                    @include('partials.header')
                @show
            @endif

        	@yield('content')

            @if(empty($isAuth))
                <Modal :_workspace="1"></Modal>
            @else
                <Modal :_workspace="{{ Auth::user()->getCurrentWorkspaceAttribute() }}"></Modal>
            @endif
            <Confirm></Confirm>
        </main>
    </div>
    @yield('message')
    @yield('scripts')
    @yield('script')
    @yield('gradient')
    <script>
        @if(!empty(Auth::user()))
            window.userId = {!! Auth::user()->id !!};
        @endif
    </script>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/fr.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.4.0/gsap.min.js"></script>
    <script src="/js/vendor/jquery.ui.widget.js"></script>
    <script src="/js/jquery.iframe-transport.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script>
        @if(!empty(Auth::user()))
            window.axios.defaults.headers.post.user = {!! Auth::user()->id !!};
            Vue.prototype.Global.user_id = window.axios.defaults.headers.post.user;
        @endif
    </script>
    <script type="text/javascript">
        let csrf = document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        $(document).ready(()=>{
            $("#submit").click(()=>{
                let files = $("#file-input")[0].files
                if(files.length > 0){
                    let formData = new FormData()
                    formData.append('file',files[0])
                    formData.append('_token',csrf)

                    $.ajax({
                        url: "{{route("freelance")}}",
                        method: 'post',
                        data: formData,
                        contentType: false,
                        dataType: 'json',
                        success: (response)=>{
                            if(response.success == 1){
                                $('#file-preview').show()
                                if(response.extension == "jpg" || response.extension == "jpeg" || response.extension == "png" ){
                                    $("#file-preview").attr("src",response.filePath);
                                    $("#file-preview").show()
                                    $(".img-header").attr("src",response.filePath);
                                    $(".img-header").show()
                                    console.log("test")
                                }
                            }
                        },
                        error: (response)=> {
                            console.log(response)
                        }
                    })
                }
            })
        })
    </script>
    <script>
        $(document).ready(()=>{
            $( "#workspaces_toggle" ).click(function() {
            $( "#workspaces_content" ).toggle();
        });
        })
    </script>
    <script type="text/javascript">
        function loadPreview(event) {
            let reader = URL.createObjectURL(event.target.files[0])
            let preview = document.getElementById("file-preview")
            preview.src = reader
            preview.style.display = "block"
            preview.style.overflow = "none"
            checkImg()
        }
        function checkImg(){
            let reader = URL.createObjectURL(event.target.files[0])
            let img = document.getElementById('file-preview')
            let back = document.querySelector(".profile-image_register ")
            let initial = document.querySelector(".image_register")
            if(img.getAttribute('src') !== "")
            {
                initial.src = reader
                initial.style.display = "none"
                initial.style.overflow = "none"
                back.style.background = "none"
            }
            else
            {
                initial.src = reader
                initial.style.display = "block"
                initial.style.overflow = "none"
            }
        }
    </script>
    <script>
        $('#select-jobs').select2({
            width: '100%',
            placeholder: "Votre Métier*",
            allowClear: false,
            closeOnSelect: true,
        });
        $('#select-skills').select2({
            width: '100%',
            placeholder: "Votre / Vos compétence(s) principale(s)*",
            allowClear: false,
            closeOnSelect: false
        });
        //$('#select-skills::placeholder').css('color',"#585266"); // cant change placeholder color in jquery
        if($('.selected_wrapper-job').find('span.error-message').length !== 0)
        {
            $('.select2-selection').addClass("is-invalid");
        }
        if($('.selected_wrapper-skills').find('span.error-message').length !== 0)
        {
            $('.select2-selection').addClass("is-invalid");
        }
    </script>
    @yield('after_scripts')
    @stack('after_scripts')
</body>
</html>
