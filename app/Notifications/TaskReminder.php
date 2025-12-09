<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TaskReminder extends Notification
{
    use Queueable;

    public $title;
    public $body;
    public $url;

    public function __construct($title, $body, $url = '/')
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->action('Buka', $this->url)
            ->icon('/images/icons/icon-192x192.png') // Pastikan icon ada
            ->badge('/images/icons/icon-192x192.png') // Icon kecil di status bar android
            ->vibrate([100, 50, 100]); // Pola getar
    }
}