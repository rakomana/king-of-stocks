<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyUsersOnStockSummary extends Notification
{
    use Queueable;

    public $summary;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($summary)
    {
        $this->summary = $summary;
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
     */
    public function toMail($notifiable)
    {
        $data = "";

        foreach(json_decode($this->summary) as $summary) {
            $data .= "{$summary->name} price is {$summary->current_price} percentage increase of {$summary->percentage_increase}% ,";
        }

        return (new MailMessage)
                    ->line('Hi '.$notifiable->name)
                    ->line($data)
                    ->action('Please check the summary below', url('/'))
                    ->line('Thank you for using our application!');
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
