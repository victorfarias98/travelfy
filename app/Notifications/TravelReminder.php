<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $travelRequest;

    public function __construct(TravelRequest $travelRequest)
    {
        $this->travelRequest = $travelRequest;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $daysUntilTravel = now()->diffInDays($this->travelRequest->departure_date);
        
        return (new MailMessage)
            ->subject('Lembrete: Sua viagem está próxima!')
            ->greeting('Olá!')
            ->line("Este é um lembrete sobre sua viagem para {$this->travelRequest->destination}.")
            ->line("Faltam {$daysUntilTravel} dia(s) para sua partida.")
            ->line("Data de Partida: {$this->travelRequest->departure_date->format('d/m/Y')}")
            ->line("Data de Retorno: {$this->travelRequest->return_date->format('d/m/Y')}")
            ->line('Não se esqueça de fazer os preparativos necessários:')
            ->line('• Documentos de viagem')
            ->line('• Reservas de hospedagem')
            ->line('• Transporte local')
            ->action('Ver Detalhes da Viagem', url("/travel-requests/{$this->travelRequest->id}"))
            ->line('Boa viagem!');
    }

    public function toArray($notifiable): array
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'destination' => $this->travelRequest->destination,
            'departure_date' => $this->travelRequest->departure_date->toDateString(),
            'return_date' => $this->travelRequest->return_date->toDateString(),
            'days_until_travel' => now()->diffInDays($this->travelRequest->departure_date)
        ];
    }
}