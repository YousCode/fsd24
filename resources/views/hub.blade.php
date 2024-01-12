<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
    />
    <link rel="dns-prefetch" href="//fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css?family=Nunito"
      rel="stylesheet"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="{{ url('/images/favicon/apple-touch-icon.png') }}"
    />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="{{ url('/images/favicon/favicon-32x32.png') }}"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="{{ url('/images/favicon/favicon-16x16.png') }}"
    />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TweenMax.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TimelineMax.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.6/ScrollMagic.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/animation.gsap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smooth-scrollbar/8.3.1/smooth-scrollbar.js"></script>

    <!-- Smooth Scroll Script-->
    <script>
      // Il s'agit du script pour smooth la scroll bar. Je verifie si je suis bien sur le navigateur chrome et je console log IsChrome pour être sur que mon script fonctionne
      $(function () {
        let isChrome =
          /Chrome/.test(navigator.userAgent) &&
          /Google Inc/.test(navigator.vendor);

        console.log("is Chrome ? ", isChrome);
        // Je définis mes variables qui vont me servir pour mon script
        let scenes = [];
        let y = 0;

        // J'initialise ma scrollbar
        let scroll = Scrollbar.init(document.querySelector(".main"));

        // J'initialise mon controller
        let controller = new ScrollMagic.Controller({
          refreshInterval: 5,
        });

        // Je fais une condition si ma première variable ischrome est valable je retourne la position y du scroll controller
        if (isChrome) {
          controller.scrollPos(function () {
            return y;
          });
        }

        // J'initialise ensuite mon scroll
        $("div.main .div").each(function () {
          let fluid = $(this).find(".main");

          let tl = new TimelineMax();
          tl.to(fluid, 1, { yPercent: -80, rotation: 0.01 }, "start");

          scenes.push(
            new ScrollMagic.Scene({
              offset: 200,
              triggerHook: "onEnter",
              triggerElement: $(this)[0],
              duration: $(window).height(),
              reverse: true,
            })
              .setTween(tl)
              .on("leave", function () {
                //console.log('leave scene');
              })
              .on("enter", function () {
                //console.log('enter scene');
              })
              .on("progress", function (e) {
                //console.log("progress => ", e.progress);
              })
              .addTo(controller)
          );
        });

        // listener smooth-scrollbar, update controller
        scroll.addListener(function (status) {
          y = status.offset.y;

          if (isChrome) {
            controller.update();
          } else {
            scenes.forEach(function (scene) {
              scene.refresh();
            });
          }
        });
      });
    </script>
    <!-- End of the script-->
    <title>Paume Plus</title>
    <style type="text/css">
      * {
        font-family: "ProximaNova-Regular", "Proxima Nova";
      }
      #myVideo {
        z-index: 0;
        width: 150%;
        margin: 0;
        padding: 0;
      }
      /* --- first part of the hub */
      body {
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        background-color: black;
        min-width: 100%;
        min-height: 100vh;
        overflow: hidden;
      }
      h3 {
        color: white;
      }
      .box-blur {
        position: absolute;
        width: 101%;
        min-width: 101%;
        height: 67vh;
        bottom: -16px;
        background: rgba(21, 12, 45, 0.8);
        backdrop-filter: blur(70px);
        -webkit-backdrop-filter: blur(70px);
        /* Note: backdrop-filter has minimal browser support */
        border-radius: 90px;
        z-index: 3;
        padding-bottom: 100px;
        overflow-y: hidden;
      }
      .main {
        position: absolute;
        width: 101%;
        height: 67vh;
      }
      .degrade {
        width: 101%;
        z-index: 90;
        height: 170px;
        position: fixed;
        top: 0;
        border-radius: 90px 90px 0px 0px;
        background: rgba(21, 10, 51, 0);
      }
      .degrade img {
        height: 170px;
        position: fixed;
        width: 100%;
        border-radius: 90px 90px 0px 0px;
      }
      .col-sm-3 {
        position: relative;
        transition: 0.5s;
        width: 280px;
        padding-top: 40px;
        padding-right: 0px;
        padding-left: 0px;
      }
      .col-sm-3:hover {
        opacity: 50%;
      }
      p {
        margin: 0;
      }
      /*------- different card part------- */
      .productivity {
        background-color: #0f0b1d;
        width: 155px;
        height: 48px;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
      }
      .productivity2 {
        background-color: #0f0b1d;
        width: 155px;
        height: 48px;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
      }
      .productivity3 {
        background-color: #0f0b1d;
        height: 48px;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
      }
      .productivitydesign {
        background-color: #0f0b1d;
        width: 201px;
        height: 48px;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
      }
      .semie-header {
        display: flex;
      }
      .product {
        font-style: normal;
        font-weight: bold;
        font-size: 20px;
        background: linear-gradient(to top right, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }
      .design {
        font-style: normal;
        font-weight: bold;
        font-size: 19px;
        background: linear-gradient(to top right, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }
      .premium {
        text-align: center;
        font-style: normal;
        font-weight: bold;
        font-size: 19px;
        background: linear-gradient(20deg, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }

      .life {
        text-align: center;
        font-style: normal;
        font-weight: bold;
        font-size: 20px;
        background: linear-gradient(to top right, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }

      .line {
        justify-content: center;
        /*text-align: center;*/
        padding-top: 15px;
        width: 932px;
      }
      .linedesign {
        justify-content: center;
        /*text-align: center;*/
        padding-top: 15px;
        width: 882px;
      }
      .line3 {
        justify-content: center;
        /*text-align: center;*/
        padding-top: 15px;
        width: 988px;
      }
      .line img {
        text-align: center;
        vertical-align: middle;
        width: 932px;
      }
      .linedesign img {
        text-align: center;
        vertical-align: middle;
        width: 882px;
      }
      .line3 img {
        text-align: center;
        vertical-align: middle;
        width: 988px;
      }
      /*------- END of different card part------- */
      .zone {
        position: relative;
        width: 259px;
        height: 48px;
        display: flex;
        background: #0f0b1d;
        border-radius: 8px;
        z-index: 10;
        margin: auto;
        justify-content: center;
      }
      .zone-click {
        position: relative;
        width: 259px;
        margin: auto;
        top: -20px;
        z-index: 10;
        padding-bottom: 120px;
      }

      .click {
        width: 26px;
        height: 26px;
        margin-right: 12px;
      }
      .message {
        position: relative;
        height: 350px;
        width: 1043px;
        margin: auto;
        padding-top: 122px;
        top: 0px;
      }

      .message h3 {
        text-align: center;
        padding-bottom: 25px;
        font-weight: 700;
        background: linear-gradient(90deg, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
        text-align: center;
        padding-bottom: 25px;
        font-weight: 700;
        font-family: "ProximaNova-Regular", "Proxima Nova";
      }
      .message p {
        text-align: justify;
        width: 597px;
        margin: auto;
        font-style: normal;
        font-weight: 400;
        position: relative;
        color: white;
      }
      .googleform {
        position: absolute;
      }
      .message img {
        margin: auto;
        height: 350px;
        width: 1043px;
        position: absolute;
      }
      .search {
        width: 820px;
        height: 60px;
        margin: auto;
        top: 100px;
        z-index: 3;
      }
      .logo-search {
        position: absolute;
      }
      .for {
        width: 820px;
        margin: auto;
        height: 60px;
        top: 100px;
        font-size: 18px;
        color: white;
      }

      .inp {
        width: 820px;
        height: 60px;
        margin: auto;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid #38007b;
        box-sizing: border-box;
        backdrop-filter: blur(30px);
        border-radius: 30px;
        outline: none;
        padding-left: 75px;
        position: fixed;
      }
      .zone p {
        position: absolute;
        top: 10px;
        color: #9584c8;
        font-style: normal;
        font-weight: normal;
        font-size: 15px;
        line-height: 18px;
        text-align: center;
      }
      strong {
        color: #9584c8;
      }
      .header {
        position: absolute;
        width: 100vw;
        top: 120px;
        margin: auto;
        left: 13px;
      }
      .go {
        position: relative;
        width: 294px;
        height: 46.54px;
        margin: auto;
        margin-bottom: 30px;
      }

      .logoo {
        width: 294px;
        height: 46.54px;
      }
      .align {
        position: relative;
        width: 1120px;
        margin: auto;
        padding-top: 99px;
        left: 13px;
      }
      .alignend {
        position: relative;
        width: 1120px;
        margin: auto;
        padding-top: 99px;
        padding-bottom: 99px;
        left: 13px;
      }
      .container-fluid,
      bg-3,
      text-center {
        display: flex;
      }
      #div-horloge {
        color: white;
      }
      .img-responsive {
        width: 280px;
        max-height: 200px;
        padding-right: 30px;
      }
      .kanye {
        display: none;
      }
      .footer {
        position: absolute;
        left: 0;
        bottom: 0px;
        width: 100%;
        background-color: #0b071c;
        color: white;
        text-align: center;
        padding: 20px;
        z-index: 20;
      }
      .add-container {
        position: relative;
        width: 316px;
        height: 65px;
        margin: auto;
        top: 0px;
        z-index: 99;
      }
      .bullet {
        position: absolute;
        right: 30px;
        width: 31px;
        height: 48px;
      }
      .add {
        width: 316px;
        height: 65px;
        margin: auto;
        background-color: #0f0b1d;
        text-align: center;
        padding: 20px;
        border-radius: 8px;
      }
      .add span {
        background: linear-gradient(90deg, #7e00ff, #ffc700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: "ProximaNova-Regular";
        font-weight: bold;
        font-size: 20px;
      }
      .add img {
        width: 10px;
        height: 10px;
      }
      .addbutton {
        width: 316px;
        height: 65px;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
      }
      .plus {
        background-color: #000000;
        width: 105px;
        height: 48px;
        border-radius: 8px;
        position: absolute;
        right: 70px;
        outline: none;
        border: none;
      }

      .plus img {
        width: 13px;
        height: 14px;
      }
      button {
        outline: none;
      }
      .profile-section {
        position: absolute;
        display: flex;
        right: 80px;
        top: 50px;
      }
      .profile-section img {
        width: 40px;
        height: 40px;
      }
      .profile-section p {
        color: white;
        padding: 12px;
        font-family: "ProximaNova-Regular";
        font-size: 15px;
        line-height: 19px;
        font-weight: 700;
      }
      .hub-header{
        position: absolute;
        top: 0px;
        right: 0;
        margin: 50px;
      }
      .hub-menu_info p strong{
        color: white;
        font-family: "ProximaNova-Regular";
        font-size:18px;
        margin-right: 10px;
       
      }
      
      .hub-button{
        background-color: transparent;
        border: none;
      }
      .form-label{
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        color: white;
      }
      .form-field{
        border: none;
        display: block;
        width: 100%;
        border-bottom: 1px solid #38007B;
        font-size: 1.75rem;
        color: white;
        background: transparent;
        outline: none;
      }
      #mdp{
        position: absolute;
        width: 345px;
        height: 446px;
        background: rgba(26, 18, 52, 0.8);
        backdrop-filter: blur(50px);
        display: none;
        right: 162px;
        top: 100px;
        z-index: 39;
        border-radius: 10px;
      }
      .account{
        display: flex;
      }
      .account img{
        width: 26px;
        height: 26px;

      }
      .form-box{
        margin-bottom: 30px;
      }
      .footer {
        position: fixed;
        left: 0;
        bottom: 0px;
        width: 100%;
        background-color: #0b071c;
        color: white;
        text-align: center;
        padding: 20px;
        z-index: 20;
        font-weight: normal;
      }
      .footer p {
        opacity: 30%;
      }
      /*------- Responsive part ------- */
      @media screen and (max-width: 1303px) {
        body {
          background-size: cover;
          background-repeat: no-repeat;
          position: relative;
          background-color: black;
          min-height: 100vh;
        }
        #myVideo {
          z-index: 0;
          width: 300%;
        }
        .kanye {
          display: block;
          color: white;
          width: 100vw;
          height: 600px;
          text-align: center;
          margin: auto;
          margin-top: 230px;
          position: absolute;
          top: 170px;
        }
        .kanye img {
          width: 223px;
          height: 279px;
        }
        .kanye h1 {
          font-size: 29px;
        }
        .kanye h3 {
          font-size: 19px;
        }
        .kanye span {
          font-size: 11px;
        }
        .box-blur {
          display: none;
        }

        .col-sm-3 {
          display: none;
        }
        .degrade {
          display: none;
        }
        .degrade img {
          display: none;
        }
        .main {
          display: none;
        }
        .col-sm-3:hover {
          display: none;
        }

        .zone {
          display: none;
        }
        .zone-click {
          display: none;
        }

        .click {
          display: none;
        }
        .zone p {
          display: none;
        }
        strong {
          display: none;
        }
        .go {
          padding-bottom: 10%;
          position: relative;
          width: 294px;
          height: 46.54px;
          margin: auto;
        }
        .search {
          width: 820px;
          height: 60px;
          margin: auto;
        }
        .logoo {
          position: absolute;
          width: 294px;
          height: 46.54px;
          margin: auto;
        }
        .align {
          display: none;
        }

        .container-fluid-one,
        bg-3,
        text-center {
          display: none;
        }

        #div-horloge {
          display: none;
        }
        .img-responsive {
          display: none;
        }
        .footer {
          position: fixed;
          left: 0;
          bottom: 0px;
          width: 100%;
          background-color: #0b071c;
          color: white;
          text-align: center;
          padding: 20px;
          z-index: 20;
          font-weight: normal;
        }
      }
      @media (min-width: 576px){
        .col-sm-3 {
        flex: none ;
        max-width: 100%; 
      }
      }
      @media screen and (max-width: 800px) {
        body {
          background-size: cover;
          background-repeat: no-repeat;
          position: relative;
          background-color: black;
          min-height: 100vh;
        }
        #myVideo {
          z-index: 0;
          width: 1400%;
        }
        .kanye {
          display: block;
          color: white;
          width: 100vw;
          height: 600px;
          text-align: center;
          margin: auto;
          margin-top: 230px;
          position: absolute;
          top: 170px;
        }
        .kanye img {
          width: 223px;
          height: 279px;
        }
        .kanye h1 {
          font-size: 29px;
        }
        .kanye h3 {
          font-size: 19px;
        }
        .kanye span {
          font-size: 11px;
        }
        .box-blur {
          display: none;
        }

        .col-sm-3 {
          display: none;
        }
        .degrade {
          display: none;
        }
        .degrade img {
          display: none;
        }
        .main {
          display: none;
        }
        .col-sm-3:hover {
          display: none;
        }

        .zone {
          display: none;
        }
        .zone-click {
          display: none;
        }

        .click {
          display: none;
        }
        .zone p {
          display: none;
        }
        strong {
          display: none;
        }
        .go {
          padding-bottom: 10%;
          position: relative;
          width: 294px;
          height: 46.54px;
          margin: auto;
        }
        .search {
          display: none;
        }
        .logoo {
          position: absolute;
          width: 294px;
          height: 46.54px;
          margin: auto;
        }
        .align {
          display: none;
        }
        .for {
          width: 700px;
          margin: auto;
          height: 60px;
          top: 100px;
          font-size: 18px;
          color: white;
        }
        .profile-section {
          position: absolute;
          display: flex;
          right: 29px;
          top: 50px;
        }
        .profile-section img {
          width: 40px;
          height: 40px;
        }
        .profile-section p {
          color: white;
          padding: 12px;
          font-family: "ProximaNova-Regular";
          font-size: 15px;
          line-height: 19px;
          font-weight: 700;
        }

        .inp {
          width: 700px;
          height: 60px;
          margin: auto;
          background: rgba(0, 0, 0, 0.5);
          border: 1px solid #38007b;
          box-sizing: border-box;
          backdrop-filter: blur(30px);
          border-radius: 30px;
          outline: none;
          padding-left: 75px;
          position: fixed;
        }

        .container-fluid-one,
        bg-3,
        text-center {
          display: none;
        }

        #div-horloge {
          display: none;
        }
        .img-responsive {
          display: none;
        }
        .footer {
          left: 0;
          bottom: 0px;
          width: 100%;
          background-color: #0b071c;
          color: white;
          text-align: center;
          padding: 20px;
          z-index: 20;
          font-weight: normal;
        }
      }

      /*------- End of Responsive part------- */
    </style>
  </head>
  <video autoplay muted loop id="myVideo">
    <source src="/images/backback.mp4" type="video/mp4" />
  </video>
  <body>
    <div class="header">
      <div class="go">
        <img class="logoo" src="/images/paume.png" alt="logo" />
      </div>
      <div class="search">
        <form
          class="for"
          method="get"
          action="https://www.google.com/search?"
          name="q"
          id="search"
        >
          <input
            class="inp"
            method="get"
            type="search"
            autofocus="autofocus"
            name="q"
          /><img
            src="/images/search.png"
            alt="search logo"
            style="
              position: absolute;
              width: 20px;
              height: 20px;
              margin: 20px;
              margin-left: 42px;
            "
          />
          <input
            type="submit"
            value="submit"
            style="position: absolute; left: -9999px"
          />
        </form>
      </div>
    </div>
    <div class="kanye">
      <h1>Oups</h1>
      <img src="/images/kanye.png" alt="kanye meme" />
      <h3>
        Merci d’agrandir ta page pour profiter de notre magnifique Hub de la
        mort qui tue.
      </h3>
      <span>
        Promis on travaille d’arrache pied pour que tu n’ai plus cette
        magnifique photo de Kanye.
      </span>
      <p>PS : Ne clique surtout pas sur ce lien !</p>
    </div>
    <div class="box-blur">
      <div class="degrade">
        <img src="/images/degrade.png" alt="" />
      </div>
      <div class="main">
        <div class="align">
          <div class="semie-header">
            <div class="productivity">
              <span class="product">Productivité</span>
            </div>
            <div class="line">
              <img src="/images/line.png" alt="line" />
            </div>
          </div>

          <div class="container-fluid bg-3 text-center">
            <br />
            <div class="row">
              <div class="col-sm-3">
                <a href="https://www.google.com/"
                  ><img
                    src="/images/google.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://kolab.app"
                  ><img
                    src="/images/kolab.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.youtube.com/"
                  ><img
                    src="/images/youtube.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a
                  href="https://www.figma.com/file/la1lEQjHr5lfyJEkOHlRxe/01_TEAM_PLAN?node-id=0%3A1"
                  ><img
                    src="/images/spaceview.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
          <br />

          <div class="container-fluid bg-3 text-center">
            <div class="row">
              <div class="col-sm-3">
                <a href="https://wetransfer.com/"
                  ><img
                    src="/images/wetransfer.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://shotdeck.com/"
                  ><img
                    src="/images/shotdeck.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.frame.io/"
                  ><img
                    src="/images/frameio.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.dropbox.com/fr/"
                  ><img
                    src="/images/dropbox.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
        </div>
        <div class="align">
          <div class="semie-header">
            <div class="productivity2">
              <span class="premium">Premium Stock</span>
            </div>
            <div class="line">
              <img src="/images/line.png" alt="line" />
            </div>
          </div>

          <div class="container-fluid bg-3 text-center">
            <br />
            <div class="row">
              <div class="col-sm-3">
                <a
                  href="https://www.shutterstock.com/fr/explore/france-stock-assets-0221?kw=shutterstock&c3apidt=p15458942753&gclid=Cj0KCQjwp86EBhD7ARIsAFkgakg6D4Du2-K_pJ4YHYenzjqwjPZVHinQV0Zp0KvjqZNiQANzhRnQqTgaAgP5EALw_wcB&gclsrc=aw.ds"
                  target="_blank"
                  ><img
                    src="/images/shutter.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.gettyimages.fr/"
                  ><img
                    src="/images/getty.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.framesdealer.com/"
                  ><img
                    src="/images/frame.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://stock.adobe.com/fr/"
                  ><img
                    src="/images/stock.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
          <br />

          <div class="container-fluid bg-3 text-center">
            <div class="row">
              <div class="col-sm-3">
                <a href="https://artlist.io/"
                  ><img
                    src="/images/artlist.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.premiumbeat.com/"
                  ><img
                    src="/images/premiumbeat.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://freesound.org/"
                  ><img
                    src="/images/freesound.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
        </div>
        <div class="align">
          <div class="semie-header">
            <div class="productivitydesign">
              <span class="design">Design inspiration</span>
            </div>
            <div class="linedesign">
              <img src="/images/line.png" alt="line" />
            </div>
          </div>

          <div class="container-fluid bg-3 text-center">
            <br />
            <div class="row">
              <div class="col-sm-3">
                <a href="https://www.pinterest.fr/"
                  ><img
                    src="/images/pinterest.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.behance.net/"
                  ><img
                    src="/images/behance.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://dribbble.com/"
                  ><img
                    src="/images/dribble.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.awwwards.com/"
                  ><img
                    src="/images/awwwards.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
          <br />

          <div class="container-fluid bg-3 text-center">
            <div class="row"></div>
          </div>
        </div>
        <div class="alignend">
          <div class="semie-header">
          
            <div class="productivity3">
              <span class="life">Fonts</span>
            </div>
            <div class="line3">
              <a href=""><img src="/images/line.png" alt="line" /></a>
            </div>
          </div>

          <div class="container-fluid bg-3 text-center">
            <br />
            <div class="row">
              <div class="col-sm-3">
                <a href="https://fonts.google.com/" target="_blank"
                  ><img
                    src="/images/googlefonts.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.dafont.com/fr"
                  ><img
                    src="/images/dafont.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.typewolf.com/"
                  ><img
                    src="/images/typewolf.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="https://www.futurefonts.xyz/" target="_blank"
                  ><img
                    src="/images/future.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
          <br />

          <div class="container-fluid bg-3 text-center">
            <div class="row">
              <div class="col-sm-3">
                <a href="https://velvetyne.fr/" target="_blank"
                  ><img
                    src="/images/velvetyne.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
              <div class="col-sm-3">
                <a href="http://www.tonditype.com/#manifesto" target="_blank"
                  ><img
                    src="/images/tonditype.png"
                    class="img-responsive"
                    style="width: 100%"
                    alt="Image"
                /></a>
              </div>
            </div>
          </div>
          <div class="alignend">
            <div class="semie-header">
              <div class="productivity3">
                <span class="life">Lifestyle</span>
              </div>
              <div class="line3">
                <img src="/images/line.png" alt="line" />
              </div>
            </div>

            <div class="container-fluid bg-3 text-center">
              <br />
              <div class="row">
                <div class="col-sm-3">
                  <a
                    href="https://deliveroo.fr/fr/?utm_source=google&utm_medium=cpc&utm_term=deliveroo&utm_campaign=%2A%2A%5EAcquisition%5ESearch%5EBrand%5EFrance%5EAll%20Cities%5E%5EExact%5EAPI%5E%5E%5E%5E%5EFR%5EStrategic%5E%C2%A311252766223&utm_loc=9056144&utm_device=c&utm_adposition=&utm_network=g&utm_targetid=aud-924953221145:kwd-52577441545&gclid=Cj0KCQjwp86EBhD7ARIsAFkgakgGDrmPbHsq_Ef1chdNu1TM393ikDtv_jscD64FUbnSHWXsBKbzLFEaAk31EALw_wcB&gclsrc=aw.ds"
                    target="_blank"
                    ><img
                      src="/images/deliveroo.png"
                      class="img-responsive"
                      style="width: 100%"
                      alt="Image"
                  /></a>
                </div>
                <div class="col-sm-3">
                  <a href="https://www.ubereats.com/fr"
                    ><img
                      src="/images/uber.png"
                      class="img-responsive"
                      style="width: 100%"
                      alt="Image"
                  /></a>
                </div>
                <div class="col-sm-3">
                  <a href="https://www.netflix.com/fr/"
                    ><img
                      src="/images/netflix.png"
                      class="img-responsive"
                      style="width: 100%"
                      alt="Image"
                  /></a>
                </div>
                <div class="col-sm-3">
                  <a
                    href="https://www.spotify.com/fr/premium/?utm_source=fr-fr_brand_contextual-mobile_text&utm_medium=paidsearch&utm_campaign=alwayson_emea_fr_performancemarketing_core_brand+contextual-mobile+text+exact+fr-fr+google&gclid=Cj0KCQjwp86EBhD7ARIsAFkgaki2M2xRO-kDU1JGFMvYJJPnVUM4TqSGHdQM2_PF9vp2MgKyYo0QZGwaAlQMEALw_wcB&gclsrc=aw.ds"
                    target="_blank"
                    ><img
                      src="/images/spotify.png"
                      class="img-responsive"
                      style="width: 100%"
                      alt="Image"
                  /></a>
                </div>
              </div>
            </div>
            <br />

            <div class="container-fluid bg-3 text-center">
              <div class="row"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
  <div class="footer">
    <p>Paume Plus - Créé et développé par Kolab © - 2021</p>
  </div>
</html>














