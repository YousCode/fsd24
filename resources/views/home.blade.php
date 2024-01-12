<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!DOCTYPE html>
  <html lang="fr">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/script.js" />
    <link href="proxima_ssv/ProximaNova-Bold.otf" rel="fonts" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@1,300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://unpkg.com/phosphor-icons"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.css" rel="stylesheet" />


    <title>kolab</title>

  </head>

<body data-hijacking="on" data-animation="scaleDown">
  <div id="header-phone">
    <img id="logo-header" src="./images/logo_kolab.png" alt="Logo" />
    <a href="{{ route('contact') }}">
      <button type="submit" id="button">{{ __('Essayer Kolab') }}</button>
    </a>
    <a href="{{ route('login') }}">
      <button type="submit" id="transparent-button">{{ __('Se connecter') }}</button>
    </a>
  </div>
  <div class="pop-hidding">
    <div id="cursor"></div>
    <div id="pointer"></div>
    <section class="headerback">
      <div id="carouselVideoExample" class="carousel slide" data-mdb-ride="carousel">
        <div class="video-container">
          <div id="header-desktop">
            <img id="logo-header" src="./images/logo_kolab.png" alt="Logo" />
            <a href="{{ route('contact') }}">
              <button type="submit" id="button">{{ __('Essayer Kolab') }}</button>
            </a>
            <a href="{{ route('login') }}">
              <button type="submit" id="transparent-button" style="width: 12%;">{{ __('Se connecter') }}</button>
            </a>
          </div>
          <div class="video-container-pill">
            <h1>
              Doutez de tout, sauf de vous.
            </h1>
            <h1 class="header-purple-title">
              Créez maintenant.
            </h1>
            <p> On s’assure du reste. </p>
          </div>
          <div class="video-container-pill-phone">
            <h1>
              Doutez de tout, </br>
              sauf de vous.
            </h1>
            <h1 class="header-purple-title">
              Créez maintenant.
            </h1>
            <p> On s’assure du reste. </p>
          </div>
        </div>

        <div id="slider-indicators" class="carousel-indicators">
          <button type="button" data-mdb-target="#carouselVideoExample" data-mdb-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-mdb-target="#carouselVideoExample" data-mdb-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-mdb-target="#carouselVideoExample" data-mdb-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">

          <div class="carousel-item active" data-mdb-interval="3000">
            <div class="overlay-video-slider">
              <video class="video-style" autoplay loop muted playsinline>
                <source src="{{url(env('APP_URL').'/resources/video/slide1.mp4') }}" type="video/mp4" />
                <source src="{{url(env('APP_URL').'/resources/video/slide1.mp4') }}" type="video/webm" />
              </video>
              <div class="video-description">
                <h1 class="video-artist"> TYLER THE CREATOR - A BOY IS A GUN </h1>
                <h1 class="video-title">
                  Réalisé par Wolf Haley.
                </h1>
              </div>
            </div>

          </div>

          <div class="carousel-item" data-mdb-interval="2900">
            <div class="overlay-video-slider">
              <video class="video-style" autoplay loop muted playsinline>
                <source src="{{url(env('APP_URL').'/resources/video/slide2.mp4') }}" type="video/mp4" />
                <source src="{{url(env('APP_URL').'/resources/video/slide2.mp4') }}" type="video/webm" />
              </video>
              <div class="video-description">
                <h1 class="video-artist">JEAN DAWSON - STARFACE </h1>
                <h1 class="video-title">
                  Réalisé par Zach Madden.
                </h1>
              </div>
            </div>
          </div>


          <div class="carousel-item" data-mdb-interval="3000">
            <div class="overlay-video-slider">
              <video class="video-style" autoplay loop muted playsinline>
                <source src="{{url(env('APP_URL').'/resources/video/slide3.mp4') }}" type="video/mp4" />
                <source src="{{url(env('APP_URL').'/resources/video/slide3.mp4') }}" type="video/webm" />
              </video>

              <div class="video-description">
                <h1 class="video-artist">The Exquisite Gucci Campaign </h1>
                <h1 class="video-title">
                  Réalisé par Alessandro Michele.
                </h1>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <section class="first-section-container">
      <div class="first-section-video-container">
        <video class="first-section-video-style" autoplay loop muted playsinline>
          <source src="{{ url(env('APP_URL').'/resources/video/dashboard.mp4') }}" type="video/mp4">
          <source src="{{ url(env('APP_URL').'/resources/video/dashboard.mp4') }}" type="video/webm">
        </video>
      </div>
      <div class="first-section-phone-video-container">
        <video class="first-section-video-phone" autoplay loop muted data-aos="fade-in" data-aos-duration="0,001s" playsinline>
          <source src="{{ url(env('APP_URL').'/resources/video/dashboard.mp4') }}" type="video/mp4">
          <source src="{{ url(env('APP_URL').'/resources/video/dashboard.mp4') }}" type="video/webm">
        </video>
        <div class="first-section-imgs-container">
          <div class="first-section-img">
            <h1>
              Kolab, la <span style="color:#9D6AFF; font-style: italic; font-weight: 400; font-family: 'Open Sans', sans-serif">plateforme</span> qui fait
              </br>
              toute la différence dans
              </br>
              votre <span style="background: #290f5a; border-radius: 3px; padding: 8px;">post-production.</span>
            </h1>
          </div>
          <div class="first-section-second-img">
            <h1>
              Kolab a ré-imaginé les outils de gestion,
              </br>
              de planification et de recrutement pour
              </br>vos projets vidéos.
              </br>
              <span style="background: #290f5a; border-radius: 8px; padding: 8px; position:relative; bottom: -5%">Du montage à l'étalonnage.</span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <section class="second-section-container">
      <div class="second-section-imgs-container">
        <div class="second-section-img">
          <h1>
            Kolab, la <span class="second-section-span-italic">plateforme</span> qui fait
            </br>
            toute la différence dans
            </br>
            votre <span class="second-section-span">post-production.</span>
          </h1>
        </div>
        <div class="second-section-arrow-div">
          <img src="images/landingPage_arrow.png">
        </div>
        <div class="second-section-second-img">
          <h1>
            Kolab a ré-imaginé les outils de gestion,
            </br>
            de planification et de recrutement pour
            </br>vos projets vidéos.
            </br>
            <span class="second-section-span1">Du montage à l'étalonnage.</span>
          </h1>
        </div>
      </div>
    </section>

    <section class="third-section-container">
      <div class="third-section-pill-container">
        <div class="third-second-section-img-container">
          <div class="third-section-pill-text">
            <h1 class="third-section-title-desktop">
              Il <span style="background: #430B7F; border-radius: 3px; margin-right: 10px; padding-left: 16px;">simplifie </span>vos briefs. 
            </h1>
            <p class="third-section-purpple-text">Nous trions et organisons inteligemment vos tâches.
              <br>
              Il ne vous reste plus qu'à les réaliser avec vos outils habituels.
            </p>
            <h1 class="third-section-title-mobile">
              Il <span style="background: #430B7F; border-radius: 3px; margin-right: 5px; padding-left: 5px;">simplifie </span>vos briefs. 
            </h1>
            <p class="third-section-purpple-mobile-text">
              Nous trions et organisons inteligemment vos tâches.
              <br>
              Il ne vous reste plus qu'à les réaliser
              <br>
              avec vos outils habituels.
            </p>
          </div>
          
        </div>
      </div>
      <div class="third-section-img-container">
        <video class="third-section-img-mask" autoplay loop muted playsinline>
          <source src="{{ url(env('APP_URL').'/resources/video/vfx-vidéo.mp4') }}" type="video/mp4">
          <source src="{{ url(env('APP_URL').'/resources/video/vfx-vidéo.mp4') }}" type="video/webm">
        </video>
      </div>
      <div class="third-section-img-container-task">
        <img src="./images/third-task.png" class="third-section-img-task">
      </div>
    </section>

    <section class="fourth-section-container">
      <div class="fourth-section-purple-pill">
        <img src="./images/Window5.png" alt="" class="fourth-section-img">
        <div class="fourth-section-pill-text">
          <h1>
            Et vous permet de suivre
          </h1>
          <h1 class="fourth-section-backgroud">
            facilement tous vos projets.
          </h1>
          <p>
            Un montage retardé ou un projet livré en avance ? <br />
            Votre planning se met à jour instantanément, passez à la suite.
          </p>
          <img src="./images/PhoneWindow.png" class="fourth-section-mobile-img">
          <img src="./images/right-planning.png" class="fourth-phone-planning">
          <h1 class="fourth-planning-text" style="font-size: 15px;
    text-align: center;
    margin: 0;
    margin-top: 20px;">Individuel, par métiers ou encore par projet, </br>
            le planning s’adaptera à tout vous.
          </h1>
        </div>
      </div>
    </section>


    <section class="fifth-section-container">
      <div class="fifth-section-video-container">
        <video class="fifth-section-video-style" autoplay loop muted playsinline>
          <source src="{{ url(env('APP_URL').'/resources/video/planning2.mp4') }}" type="video/mp4">
          <source src="{{ url(env('APP_URL').'/resources/video/planning2.mp4') }}" type="video/webm">
        </video>
        <div class="fifth-section-text-container">
          <div class="fifth-section-text-title">
            <h1 class="fifth-section-first-title">
              Individuel, par métiers, ou encore par projet,
            </h1>
            <div class="fifth-section-second-title">
              <h1 class="fifth-section-purple-second-title">
                le planning s’adaptera à
              </h1>
              <h1 class="cross">
                tout
              </h1>
              <h1 class="fifth-section-purple-third-title">
                vous.
              </h1>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="sixth-desktop" id="panels" style="
        background-color: #0a021c;
">
      <div class="pin-spacer" style="
    scroll-snap-align: end;
    scroll-snap-stop: normal;
    transition-duration: 0ms;
    min-width: 100%;
    max-width: 100%;
    ">


        <div id="panels-container" style="width: 200%;">
          <article id="panel-1" class="panel">
            <div class="container">
              <div class="row" style="
    margin: auto;
    position: relative;
    top: 6%;
    ">
                <div class="col-6 d-flex flex-column" style="width: 85%;">
                  <div class="sixth-section-text-title">
                    <h1 class="sixth-section-first-title" style="margin-top: 80px; text-align: center;">
                      Enfin une vision claire de vos<br />
                      projets vidéos.
                    </h1>
                    <video class="sixth-section-video-style" autoplay loop muted playsinline>
                      <source src="{{ url(env('APP_URL').'/resources/video/test3.mp4') }}" type="video/mp4" />
                      <source src="{{ url(env('APP_URL').'/resources/video/test3.mp4') }}" type="video/webm" />
                    </video>
                  </div>
                </div>
              </div>
            </div>
          </article>
          <article id="panel-2" class="panel">
            <div class="container">
              <div class="row">

                <div class="col-6 d-flex flex-column">

                  <div class="sixth-section-text-title" style="
                      margin: auto;
                      text-align: center;
                      align-items: center;
                      justify-content: center;
                      color: white;
                      right: -20%;
                      position: relative;
                      ">
                    <h1 class="sixth-section-first-title" style="margin-top: 80px">
                      Plus besoin de réclamer la
                      <br> <img src="images/typo.png">
                    </h1>
                    <h2 class="sixth-section-text-small-title">
                      Ou de chercher les Wetransfer dans vos emails.
                    </h2>
                    <video class="sixth-section-video-style2" type="video/mp4" autoplay loop muted playsinline>
                      <source src="{{ url(env('APP_URL').'/resources/video/landing_page_conv.mp4') }}" type="video/mp4" />
                      <source src="{{ url(env('APP_URL').'/resources/video/landing_page_conv.mp4') }}" type="video/webm" />
                    </video>
                  </div>
                </div>
              </div>
          </article>
        </div>

    </section>
    <section id="sixth-mobile" class="sixth-section-container">
      <div class="slider">
        <div class="sixth-section-text-title">
          <h1 class="sixth-section-first-title">
            Enfin une vision claire de vos projets <br />
            vidéos.
          </h1>
          <video class="sixth-section-video-style" autoplay loop muted playsinline>
            <source src="{{ url(env('APP_URL').'/resources/video/test3.mp4') }}" type="video/mp4" />
            <source src="{{ url(env('APP_URL').'/resources/video/test3.mp4') }}" type="video/webm" />
          </video>
        </div>

        <div class="sixth-section-text-title">
          <h1 class="sixth-section-second-title">
            Et plus besoin de réclamer la
            <br> <img class="sixth-typo-img" src="images/typo.png">
          </h1>
          <h2 style="font-size: 15px; line-height: 1.089; z-index: 3; font-weight: 700;"> Ou de chercher les Wetransfer dans vos emails.</h2>
          <video class="sixth-section-video-style2" autoplay loop muted playsinline>
            <source src="{{ url(env('APP_URL').'/resources/video/landing_page_conv.mp4') }}" type="video/mp4" />
            <source src="{{ url(env('APP_URL').'/resources/video/landing_page_conv.mp4') }}" type="video/webm" />
          </video>
        </div>
      </div>

      <div class="progress-bar">
        <div class="prog-bar-inner"></div>
      </div>
    </section>

    <section class="seventh-section-container">
      <div class="seventh-section-img-container">
        <img src="./images/Window8.png" class="seventh-section-img-style">
        <div class="seventh-section-title">
          <h1> Vous pourrez garder</h1>
          <h1 id="seventh-breakline-tittle">le contact facilement... </h1>
        </div>
        </img>
      </div>
    </section>

    <section class="eigth-section-container">
      <div class="eigth-section-pill-container">
        <div class="eigth-section-gradient-container">
          <div class="eigth-section-text">
            <h1>
              ...avec vos nouveaux collègues, <br />
              sans oublier les anciens.
            </h1>
            <p id="eigth-section-text-desktop">
              Retrouver facilement les informations sur un motion designer ou encore <br />
              graphiste 3D avec lequel vous avez déjà travaillé.
            </p>
            <p id="eigth-section-text-mobile">
              Retrouver facilement les informations sur un motion designer ou <br /> encoregraphiste 3D avec lequel vous avez déjà travaillé.
            </p>
          </div>
          <img src="./images/windowdrop.png" class="eigth-section-img-style">
          <img src="./images/cartes.png" class="eigth-section-mobile-img" style="width: 100%; margin-top: 2.7%;">

        </div>
      </div>
    </section>

    <section class="ninth-section-container">
      <div class="ninth-section-img-container">
        <img src="./images/Window11.png" class="ninth-section-img-style">
        <div class="ninth-section-title">
          <div class="ninth-section-title1">
            <h1 class="ninth-section-top-tittle"> Il y a encore quelque chose </h1>
            <h1 id="breakpoint-ninth-section">pour vous</h1>
          </div>
          <div class="ninth-section-title2">
            <h1>et ce n’est pas rien.</h1>
          </div>
        </div>
        </img>
      </div>
    </section>

    <section class="tenth-section-container">
      <div class="tenth-section-pill-container">
        <div class="tenth-section-gradient-container">
          <div class="tenth-section-video-style">
            <video autoplay loop muted playsinline>
              <source src="{{ url(env('APP_URL').'/resources/video/landingpage_explorer.mp4') }}" type="video/mp4" />
              <source src="{{ url(env('APP_URL').'/resources/video/landingpage_explorer.mp4') }}" type="video/webm" />
            </video>
          </div>
          <div class="tenth-section-text">
            <h1>
              Talent Explorer
            </h1>
            <div class="tenth-section-text-div">
              <h2 id="tenth-desktop">Comme une impression de </h2>
              <h2 id="tenth-desktop" class="tenth-highlited-title">jamais-vu.</h2>
              <h2 id="tenth-mobile">Comme une impression de jamais vu </h2>
            </div>
            <p class="desktop-title"> Kolab est la première plateforme qui rassemble <br>
              les meilleurs freelances dans la post-production, dans le monde entier. </p>
            <p class="phone-title"> Kolab est la première plateforme qui rassemble <br>
              les meilleurs freelances dans la post-production,
              <br> dans le monde entier.
            </p>
          </div>
        </div>
      </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/phosphor-icons"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="	https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js"></script>
    <script src="https://unpkg.co/gsap@3/dist/gsap.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/ScrollTrigger.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/ScrollToPlugin.min.js"> </script>

    <script>
      gsap.registerPlugin(ScrollToPlugin, ScrollTrigger);

      /* Main navigation */
      let panelsSection = document.querySelector("#panels"),
        panelsContainer = document.querySelector("#panels-container"),
        tween;
      document.querySelectorAll(".anchor").forEach(anchor => {
        anchor.addEventListener("click", function(e) {
          e.preventDefault();
          let targetElem = document.querySelector(e.target.getAttribute("href")),
            y = targetElem;
          if (targetElem && panelsContainer.isSameNode(targetElem.parentElement)) {
            let totalScroll = tween.scrollTrigger.end - tween.scrollTrigger.start,
              totalMovement = (panels.length - 1) * targetElem.offsetWidth;
            y = Math.round(tween.scrollTrigger.start + (targetElem.offsetLeft / totalMovement) * totalScroll);
          }
          gsap.to(window, {
            scrollTo: {
              x: x,
              autoKill: false
            },
            duration: 1
          });
        });
      });

      /* Panels */
      const panels = gsap.utils.toArray("#panels-container .panel");
      tween = gsap.to(panels, {
        xPercent: -100 * (panels.length - 1),
        ease: "none",
        scrollTrigger: {
          trigger: "#panels-container",
          pin: true,
          start: "top top",
          scrub: 1,
          snap: {
            snapTo: 1 / (panels.length - 1),
            inertia: false,
            duration: {
              min: 0.1,
              max: 0.1
            }
          },
          end: () => "+=" + (panelsContainer.offsetWidth - innerWidth)
        }
      });
    </script>

    <script>
      const slider = document.querySelector(".sixth-section-text-title");
      const progressBar = document.querySelector(".prog-bar-inner");
      let sliderGrabbed = false;
      slider.parentElement.addEventListener("scroll", (e) => {
        progressBar.style.width = `${getScrollPercentage()}50%`;
      });
      slider.addEventListener("mousedown", (e) => {
        sliderGrabbed = true;
        slider.style.cursor = "grabbing";
      });
      slider.addEventListener("mouseup", (e) => {
        sliderGrabbed = true;
        slider.style.cursor = "grab";
      });
      slider.addEventListener("mouseleave", (e) => {
        sliderGrabbed = false;
      });
      slider.addEventListener("mousemove", (e) => {
        if (sliderGrabbed) {
          slider.parentElement.scrollLeft -= e.movementX;
        }
      });
      slider.addEventListener("mousewheel", (e) => {
        e.preventDefault();
        slider.parentElement.scrollLeft += e.deltaX;
      });

      function getScrollPercentage() {
        return (
          (slider.parentElement.scrollLeft /
            (slider.parentElement.scrollWidth - slider.parentElement.clientWidth)) *
          100
        );
      }
    </script>

    <script>
      (function() {

        var doc = document.documentElement;
        var w = window;


        var curScroll;
        var prevScroll = w.scrollY || doc.scrollTop;
        var curDirection = 0;
        var prevDirection = 0;

        var header = document.getElementById('header-desktop');
        var toggled;
        var threshold = 200;

        var checkScroll = function() {
          curScroll = w.scrollY || doc.scrollTop;
          if (curScroll > prevScroll) {
            // scrolled down
            curDirection = 2;
          } else {
            //scrolled up
            curDirection = 1;
          }

          if (curDirection !== prevDirection) {
            toggled = toggleHeader();
          }

          prevScroll = curScroll;
          if (toggled) {
            prevDirection = curDirection;
          }
        };

        var toggleHeader = function() {
          toggled = true;
          if (curDirection === 2 && curScroll > threshold) {
            header.classList.add('hide');
          } else if (curDirection === 1) {
            header.classList.remove('hide');
          } else {
            toggled = false;
          }
          return toggled;
        };

        window.addEventListener('scroll', checkScroll);

      })();

      (function() {

        var doc = document.documentElement;
        var w = window;


        var curScroll;
        var prevScroll = w.scrollY || doc.scrollTop;
        var curDirection = 0;
        var prevDirection = 0;

        var header = document.getElementById('header-phone');
        var toggled;
        var threshold = 200;

        var checkScroll = function() {
          curScroll = w.scrollY || doc.scrollTop;
          if (curScroll > prevScroll) {
            // scrolled down
            curDirection = 2;
          } else {
            //scrolled up
            curDirection = 1;
          }

          if (curDirection !== prevDirection) {
            toggled = toggleHeader();
          }

          prevScroll = curScroll;
          if (toggled) {
            prevDirection = curDirection;
          }
        };

        var toggleHeader = function() {
          toggled = true;
          if (curDirection === 2 && curScroll > threshold) {
            header.classList.add('hide');
          } else if (curDirection === 1) {
            header.classList.remove('hide');
          } else {
            toggled = false;
          }
          return toggled;
        };

        window.addEventListener('scroll', checkScroll);

      })();
    </script>
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
    <script src="https://cdn.rawgit.com/JacobLett/bootstrap4-latest/master/bootstrap-4-latest.min.js"></script>

    <script>
      $(document).ready(function() {
        var $videoSrc;
        $('.video-btn').click(function() {
          $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);
        $('#myModal').on('shown.bs.modal', function(e) {
          $("#video").attr('src', $videoSrc + "?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;autoplay=1");
        })
        $('#myModal').on('hide.bs.modal', function(e) {
          $("#video").attr('src', $videoSrc);
        })
      });
    </script>

    <script>
      $(document).ready(function() {
        var $videoSrc;
        $('.video-btn-phone').click(function() {
          $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);
        $('#myModalPhone').on('shown.bs.modal', function(e) {
          $("#videophone").attr('src', $videoSrc + "?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;autoplay=1");
        })
        $('#myModalPhone').on('hide.bs.modal', function(e) {
          $("#videophone").attr('src', $videoSrc);
        })
      });
    </script>

    <script src="https://player.vimeo.com/api/player.js"></script>

    <section class="eleventh-section-container">
      <div class="eleventh-section-vids-container">
        <h1>Projets gérés sur <span>Kolab</span></h1>
        <div class="eleventh-section-left-container">
          <div style="display: flex; justify-content: center; margin-top: 50px">
            <div>
              <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn" src="./images/djsnake1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/618855811?h=ea543f4006&amp;badge=0&amp;autopause=0;player_id=0&amp;app_id=58479" data-target="#myModal" />
              <h2 style="margin-left: 30px">BIG BANG MAGHENTA</h2>
              <h4 style="margin-left: 30px">DJ SNAKE X HUBLOT</h4>
            </div>
            <div>
              <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn" src="./images/damso1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/625501320?h=158f552e0c&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" allow="autoplay" data-target="#myModal" />
              <h2 style="margin-left: 30px">DU MAL À TE DIRE</h2>
              <h4 style="margin-left: 30px">DINOS, DAMSO</h4>
            </div>
            <div>
              <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn" src="./images/angele1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/655356790?h=2b5283b1fc&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModal" />
              <h2 style="margin-left: 30px">DÉMONS </h2>
              <h4 style="margin-left: 30px">ANGÈLE, DAMSO</h4>
            </div>
          </div>
        </div>
        <div class="eleventh-section-left-container" style="margin-top: -70px">
          <div style="display: flex; justify-content: center; margin-top: 100px">
            <div style="">
              <img style=" border-radius: 20px" class="eleventh-section-bon-img video-btn" src="./images/piano1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/572571146?h=1bb541ed44&amp;title=0&amp;byline=0&amp;portrait=0&amp;speed=0&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModal" />
              <h2 style="margin-left: 30px">ALBA</h2>
              <h4 style="margin-left: 30px">BON ENTENDEUR, SOFIANE PALMART</h4>
            </div>
            <div>
              <img style=" border-radius: 20px" class="eleventh-section-bon-img video-btn" src="./images/louisv.png" data-toggle="modal" data-src="https://player.vimeo.com/video/618844156?h=ec3eaa40de&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModal" />
              <h2 style="margin-left: 30px">MEN F/W 21</h2>
              <h4 style="margin-left: 30px">LOUIS VUITTON</h4>
            </div>
            <div>
              <img style=" border-radius: 20px" class="eleventh-section-bon-img video-btn" src="./images/neymar1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/499212600?h=e7f65e8b09&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModal" />
              <h2 style="margin-left: 30px">FUTURE 2</h2>
              <h4 style="margin-left: 30px">PUMA COMMERCIAL</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background: rgb(14, 0, 36); opacity: 0.978; backdrop-filter: blur(40px);">
        <div class="modal-dialog" role="document" style="margin:0;padding-left: 2%;padding-right: 4%;padding-top: 20px;">
          <div class="modal-content" style="width: 90vw;height: 95vh;">


            <div class="modal-body">

              <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1;color: white;top: -5%;left: -6%;padding: 15px;font: 700;padding-top: 10px;">
                <span aria-hidden="true" style="font-size: 60px; color: white; font-weight: 200">&times;</span>
              </button>
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="" id="video" allow="autoplay " allowscriptaccess="always"></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>



  </div>
  </section>


  <section class="eleventh-section-container-mobile">
    <div class="eleventh-section-vids-container">
      <h1>Projets gérés sur <span>Kolab</span></h1>
      <div class="eleventh-section-left-container">
        <div class="eleventh-section-left-div">
          <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn-phone" src="./images/img1.png" data-toggle="modal" data-src="https://player.vimeo.com/video/618855811?h=ea543f4006&amp;badge=0&amp;autopause=0;player_id=0&amp;app_id=58479" data-target="#myModalPhone" />
          <h2>big bang magentha</h2>
          <h4>dj snake x hublot</h4>
        </div>

        <div class="eleventh-section-right-div">
          <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn-phone" src="./images/img3.png" data-toggle="modal" data-src="https://player.vimeo.com/video/655356790?h=2b5283b1fc&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModalPhone" />
          <h2>démons</h2>
          <h4>Angele & damso</h4>
        </div>

        <div class="eleventh-section-right-container">
          <div class="eleventh-section-right-div">
            <img style=" border-radius: 20px" class="eleventh-section-left-img video-btn-phone" src="./images/img2.png" data-toggle="modal" data-src="https://player.vimeo.com/video/625501320?h=158f552e0c&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" allow="autoplay" data-target="#myModalPhone" />
            <h2>DU MAL à te dire</h2>
            <h4>Dinos, Damso</h4>
          </div>

          <div class="eleventh-section-right-div">
            <img style="border-radius: 20px; " class="eleventh-section-left-img video-btn-phone" src="./images/img4.png" data-toggle="modal" data-src="https://player.vimeo.com/video/572571146?h=1bb541ed44&amp;title=0&amp;byline=0&amp;portrait=0&amp;speed=0&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModalPhone" />
            <h2>ALBA</h2>
            <h4>Bon entendeur, Sofiane Pamart</h4>
          </div>

          <div class="eleventh-section-right-div">
            <img style="border-radius: 20px; " class="eleventh-section-left-img video-btn-phone" src="./images/louisvuitton.png" data-toggle="modal" data-src="https://player.vimeo.com/video/618844156?h=ec3eaa40de&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModalPhone" />
            <h2>Men FW21</h2>
            <h4>Louis Vuitton
            </h4>
          </div>

          <div class="eleventh-section-right-div">
            <img style="border-radius: 20px; " class="eleventh-section-left-img video-btn-phone" src="./images/neymar.png" data-toggle="modal" data-src="https://player.vimeo.com/video/499212600?h=e7f65e8b09&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" data-target="#myModalPhone" />
            <h2>FUTUR 2</h2>
            <h4>Puma commercial</h4>
          </div>
        </div>
      </div>

      <div class="modal fade" id="myModalPhone" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background: #0E0024; opacity: 0.978;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="width: 100%; margin-top: 250px; padding:0;">


            <div class="modal-body">

              <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1;color: white; left: 4%; top: -100%;">
                <span aria-hidden="true" style="font-size: 30px; color: white; font-weight: 400;">&#8592;</span>
              </button>
              <!-- 16:9 aspect ratio -->
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="" id="videophone" allow="autoplay playsinline" allowscriptaccess="always"></iframe>
              </div>


            </div>

          </div>
        </div>
      </div>
    </div>
  </section>


  <section class="footer-section-container">
    <div class="footer-section-background">
      <h1 class="footer-section-title" id="footer-desktop-screen" style="
    position: relative;
    top: 16%;
">
        Essayez dès maintenant
      </h1>
      <div class="footer-section-price-container">
        <h1 class="footer-section-title" id="footer-mobile-screen">
          Essayez dès maintenant
        </h1>
        <div style="display: flex; justify-content: center; align-items: end;position:relative; margin-left: 30px">
          <img class="footer-section-logo" src="./images/logo.png" />
          <h5 class="price-footer">€</h5>
          <h1 class="number-price-footer">29,99</h1>
          <h1 class="slash-price-footer">/</h1>
          <div class="description-footer-section">
            <h1 class="description-price-footer">par utilisateur mensuel </h1>
            <h1 class="purple-description-price-footer">gratuit pour les freelances</h1>
          </div>
        </div>
        <div class="height: 100%;position: relative; width: 100%;">
          <a href="{{ route('contact') }}">
            <button type="submit" id="footer-section-button">
              {{ __('Obtenir une démo ')}}
              <img class="footer-section-arrow" src="./images/arrow.png" />
            </button>
          </a>
        </div>
      </div>
      <div class="infos">
        <div style="text-align: left" class="column">
          <h3>Contact</h3>

          <p>hello@kolab.app</p>
        </div>
        <div class="column" style="text-align: left">
          <h3>Réseaux</h3>
          <a href="https://www.instagram.com/kolab.app/">
            <p type="submit" href="https://www.instagram.com/kolab.app/">
              @kolab.app
            </p>
          </a>
        </div>
        <div class="column">
          <p>CGV</p>

        </div>
        <div class="column">
          <p>Mentions Légales</p>
        </div>
      </div>
      <div class="droits-reserves" style="color:#C0C0C0;">
        <p style="line-height: 5;">© Kolab 2023 - Tous droits réservés</p>
      </div>
  </section>
</body>
<script>
  AOS.init();
</script>
