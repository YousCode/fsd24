<?php

namespace App\Notifications;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewContactRegistration extends Notification
{
    use Queueable;

    private $newContact;

    /**
     * Create a new notification instance.
     *
     * @param  Contact  $newContact
     * @return void
     */
    public function __construct(Contact $newContact)
    {
        $this->newContact = $newContact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     * @return \SendinBlue\Client\Model\SendSmtpEmail
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('New contact has been made with ' . $this->newContact->email)
            ->action('Approve new contact', route('unlock', $this->newContact->id));
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
