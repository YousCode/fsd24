<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification
{
    use Queueable;

    protected $details;

    /**
     * Create a new notification instance.
     *
     * @param array $details
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [\App\Channels\SendinblueChannel::class];
    }

    /**
     * Get the Sendinblue representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSendinblue($notifiable)
    {
        return [
            $this->details['template_id'],
            $this->details['receivers'],
            $this->details['params'],
            $this->details['attachment']
        ];
    }
}
