<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTravelRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $travelRequest;

    public function __construct(TravelRequest $travelRequest)
    {
        $this->travelRequest = $travelRequest;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nova Solicitação de Viagem Corporativa')
            ->greeting('Olá, Administrador!')
            ->line('Uma nova solicitação de viagem foi criada e aguarda sua análise.')
            ->line("Solicitante: {$this->travelRequest->user->name}")
            ->line("Destino: {$this->travelRequest->destination}")
            ->line("Data de Partida: {$this->travelRequest->departure_date->format('d/m/Y')}")
            ->line("Data de Retorno: {$this->travelRequest->return_date->format('d/m/Y')}")
            ->action('Analisar Solicitação', url("/admin/travel-requests/{$this->travelRequest->id}"))
            ->line('Por favor, analise e aprove ou rejeite a solicitação.');
    }

    public function toArray($notifiable): array
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'user_id' => $this->travelRequest->user_id,
            'user_name' => $this->travelRequest->user->name,
            'destination' => $this->travelRequest->destination,
            'departure_date' => $this->travelRequest->departure_date->toDateString(),
            'return_date' => $this->travelRequest->return_date->toDateString(),
            'message' => "Nova solicitação de viagem de {$this->travelRequest->user->name} para {$this->travelRequest->destination}."
        ];
    }
}
