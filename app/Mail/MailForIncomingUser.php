<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Webup\LaravelSendinBlue\SendinBlue;
use Webup\LaravelSendinBlue\SendinBlueTransport;

class MailForIncomingUser extends Mailable
{
    use Queueable, SerializesModels;
    use SendinBlue;

    public $text;


    public function __construct($text)
    {
        $this->text = $text;

    }


    public function build()
    {
        return $this
            ->view([])
            ->subject(SendinBlueTransport::USE_TEMPLATE_SUBJECT)
            ->sendinblue(
                [
                    'template_id' => 9,
                    'from'=>'test@paume.paris',
                    'subject'=>'test',
                    'to'=>'[bryan@paume.paris]',
                    'tags'  =>['contacts'],
                    'params'    =>  [
                        'FIRSTNAME' => 'Bryan',

                    ],
                ]
            );

    }
}
