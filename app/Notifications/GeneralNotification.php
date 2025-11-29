<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Simpan ke database
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'time' => now(),
        ];
    }
}