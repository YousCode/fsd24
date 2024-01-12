<?php

namespace App\Channels;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class SendinblueChannel
{
    public function apiInstance()
    {
        $key = env('SENDINBLUE_API_V3');
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $key);

        return new TransactionalEmailsApi(
            new Client(),
            $config
        );
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return mixed
     */
    public function send($notifiable, Notification $notification)
    {
        $api = $this->apiInstance();
        list($template_id, $receivers, $params, $attachment) = $notification->toSendinblue($notifiable);
        $to = [];

        foreach ($receivers as $receiver) {
            $to[] = [
                'email' => $receiver
            ];
        }

        $smtpConfig = [
            'templateId' => intval($template_id),
            'to' => $to,
            'sender' => [
                "email" => config('mail.from.address'),
                'name' => 'Kolab'
            ],
            'params' => $params,
        ];

        if (!empty($attachment)) {
            $smtpConfig['attachment'] = [
                ['url' => $attachment]
            ];
        }

        $emailTo = new SendSmtpEmail($smtpConfig);

        try {
            $api->sendTransacEmail($emailTo);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}