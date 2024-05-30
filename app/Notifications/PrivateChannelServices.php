<?php

namespace App\Notifications;

use App\Events\PusherNotificationPrivateEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PrivateChannelServices extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [DatabaseChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }


    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


    public function toDatabase($notifiable)
    {
        event(new PusherNotificationPrivateEvent($this->id, $this->data, $notifiable));
        return [
            'id' =>  $this->id,
            'for_admin' =>  0,
            'notification' => $this->data,
        ];
    }
}
