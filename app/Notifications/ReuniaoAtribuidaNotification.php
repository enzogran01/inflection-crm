<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReuniaoAtribuidaNotification extends Notification
{
    use Queueable;

    public $reuniao;

    /**
     * Create a new notification instance.
     */
    public function __construct($reuniao)
    {
        $this->reuniao = $reuniao;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'reuniao',
            'reuniao_id' => $this->reuniao->id,
            'titulo' => $this->reuniao->titulo,
            'mensagem' => '"' . $this->reuniao->titulo . '" no dia ' . $this->reuniao->inicio->format('d/m/Y \à\s H:i'),
            'url' => '/reunioes?tableAction=view&tableActionRecord=' . $this->reuniao->id,
        ];
    }
}
