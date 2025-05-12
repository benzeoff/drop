<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail']; // Можно добавить 'database' для отображения в интерфейсе
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ваше бронирование завершено')
            ->line('Ваше бронирование в Drop Cyber Lounge завершено.')
            ->line("Ресурс: {$this->booking->resource->name}")
            ->line("Время: с " . $this->booking->start_time . " по " . $this->booking->end_time)
            ->line("Стоимость: {$this->booking->price} ₽")
            ->line('Спасибо за использование наших услуг!');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'Ваше бронирование завершено.',
            'resource' => $this->booking->resource->name,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
            'price' => $this->booking->price,
        ];
    }
}
