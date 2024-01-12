<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{ url()->full() }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Metas -->
    <meta name="description" content="@yield('meta_description')"/>

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>Kolab - Paiement du devis de {{ $proposition->freelance->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>

<div class="explorer-quote-payment-page">

    <img class="kolab-logo" src="/images/explorer/kolab-logo.png" alt="Kolab Logo"/>

    <div class="explorer-quote-payment-card">
        <div class="quote-payment-card-header">
            <h1 class="quote-payment-card-title">Paiement du devis de {{ $proposition->freelance->name }}</h1>
        </div>
        <div class="quote-payment-card-body">
            <div class="quote-payment-card-summary">
                <div class="summary-item">
                    <div class="icon-container"><span class="picto-explorer-pile"/></div>
                    <div class="summary-item-infos">
                        <div class="summary-item-title">Inclus dans ce que vous payez</div>
                        <ul>
                            @foreach(json_decode($quote->quote_line) as $quoteLine)
                                <li class="summary-item-value">
                                    {{$quoteLine}}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                    <div class="summary-item-infos">
                        <div class="summary-item-title">Livraison de votre commande</div>
                        <div class="summary-item-value">{{ $quote->deadline }}</div>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="icon-container"><span class="picto-explorer-eur"/></div>
                    <div class="summary-item-infos">
                        <div class="summary-item-title">Prix</div>
                        <div class="summary-item-value">{{ $quote->price }} € HT</div>
                    </div>
                </div>
                @if($quote->service_fee == '')
                    <div class="summary-item">
                        <div class="icon-container"><span class="picto-explorer-shield"/></div>
                        <div class="summary-item-infos">
                            <div class="summary-item-title">Frais de service</div>
                            <div class="summary-item-value">{{ $quote->service_fee }} € HT</div>
                        </div>
                    </div>
                @endif
                <div class="summary-item">
                    <div class="icon-container"><span class="picto-explorer-shield"/></div>
                    <div class="summary-item-infos">
                        <div class="summary-item-title">Commentaires</div>
                        <div class="summary-item-value">{{ $quote->comments }}</div>
                    </div>
                </div>
            </div>
            <div class="quote-payment-card-card-infos">
                <form
                    role="form"
                    action="{{ route('explorer.quote.checkout',['quote_id'=>$quote->id]) }}"
                    method="post"
                    class="require-validation card-infos-form"
                    data-cc-on-file="false"
                    data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                    id="payment-form">
                    @csrf

                    <div class='form-row row'>
                        <div class='col-xs-12 form-group required'>
                            <label class='control-label card-infos-form-label'>Nom sur la carte</label> <input
                                class='form-control card-infos-form-input' size='4' type='text'>
                        </div>
                    </div>

                    <div class='form-row row'>
                        <div class='col-xs-12 form-group card required'>
                            <label class='control-label card-infos-form-label'>Numéro de carte</label> <input
                                autocomplete='off' class='form-control card-number card-infos-form-input' size='20'
                                type='text'>
                        </div>
                    </div>

                    <div class='form-row row'>
                        <div class='col-xs-12 col-md-4 form-group cvc required'>
                            <label class='control-label card-infos-form-label'>CVC</label> <input autocomplete='off'
                                                                                                  class='form-control card-cvc card-infos-form-input'
                                                                                                  placeholder='ex. 311'
                                                                                                  size='4'
                                                                                                  type='text'>
                        </div>
                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                            <label class='control-label card-infos-form-label'>Mois</label> <input
                                class='form-control card-expiry-month card-infos-form-input' placeholder='MM' size='2'
                                type='text'>
                        </div>
                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                            <label class='control-label card-infos-form-label'>Année</label> <input
                                class='form-control card-expiry-year card-infos-form-input' placeholder='YYYY' size='4'
                                type='text'>
                        </div>
                    </div>

                    <div class='form-row row'>
                        <div class='col-md-12 error form-group hide'>
                            <div class='alert-danger alert'>Please correct the errors and try
                                again.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            @if (Session::has('success'))
                                <button class="btn btn-primary btn-lg btn-block card-infos-form-button-success" disabled
                                        type="button">
                                    {{ Session::get('success') }}
                                </button>
                            @else
                                <button class="btn btn-primary btn-lg btn-block card-infos-form-button" type="submit">
                                    Payer
                                    {{$quote->price}} €
                                </button>
                            @endif
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

</body>

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
    $(function () {

        var $form = $(".require-validation");

        $('form.require-validation').bind('submit', function (e) {
            var $form = $(".require-validation"),
                inputSelector = ['input[type=email]', 'input[type=password]',
                    'input[type=text]', 'input[type=file]',
                    'textarea'].join(', '),
                $inputs = $form.find('.required').find(inputSelector),
                $errorMessage = $form.find('div.error'),
                valid = true;
            $errorMessage.addClass('hide');

            $('.has-error').removeClass('has-error');
            $inputs.each(function (i, el) {
                var $input = $(el);
                if ($input.val() === '') {
                    $input.parent().addClass('has-error');
                    $errorMessage.removeClass('hide');
                    e.preventDefault();
                }
            });

            if (!$form.data('cc-on-file')) {
                e.preventDefault();
                Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                Stripe.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
            }

        });

        function stripeResponseHandler(status, response) {
            if (response.error) {
                $('.error')
                    .removeClass('hide')
                    .find('.alert')
                    .text(response.error.message);
            } else {
                /* token contains id, last4, and card type */
                var token = response['id'];

                $form.find('input[type=text]').empty();
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
            }
        }

    });

    @if (Session::has('success'))
    setTimeout(function () {
        window.location.href = "/explorer/messenger";
    }, 3000);
    @endif
</script>
<script src="{{ mix('js/app.js') }}"></script>
{{-- <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script>moment.locale('fr');</script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/fr.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/vendor/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
</html>
