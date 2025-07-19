<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $travelRequest;
    protected $newStatus;

    public function __construct(TravelRequest $travelRequest, string $newStatus)
    {
        $this->travelRequest = $travelRequest;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusMessages = [
            'approved' => [
                'subject' => 'Pedido de Viagem Aprovado!',
                'greeting' => 'Boa notícia!',
                'line1' => 'Seu pedido de viagem foi aprovado.',
                'line2' => 'Você pode começar a fazer os preparativos para sua viagem.',
                'action' => 'Ver Detalhes do Pedido'
            ],
            'cancelled' => [
                'subject' => 'Pedido de Viagem Cancelado',
                'greeting' => 'Informação sobre seu pedido',
                'line1' => 'Seu pedido de viagem foi cancelado.',
                'line2' => 'Entre em contato com o RH se tiver dúvidas sobre o cancelamento.',
                'action' => 'Ver Detalhes do Pedido'
            ]
        ];

        $messages = $statusMessages[$this->newStatus] ?? [
            'subject' => 'Atualização do Pedido de Viagem',
            'greeting' => 'Atualização do seu pedido',
            'line1' => "O status do seu pedido foi atualizado para: {$this->newStatus}",
            'line2' => 'Verifique os detalhes do pedido para mais informações.',
            'action' => 'Ver Detalhes do Pedido'
        ];

        return (new MailMessage)
            ->subject($messages['subject'])
            ->greeting($messages['greeting'])
            ->line($messages['line1'])
            ->line("Destino: {$this->travelRequest->destination}")
            ->line("Data de Partida: {$this->travelRequest->departure_date->format('d/m/Y')}")
            ->line("Data de Retorno: {$this->travelRequest->return_date->format('d/m/Y')}")
            ->line($messages['line2'])
            ->action($messages['action'], url("/travel-requests/{$this->travelRequest->id}"))
            ->line('Obrigado por usar nossa plataforma de viagens corporativas!');
    }

    public function toArray($notifiable): array
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'destination' => $this->travelRequest->destination,
            'departure_date' => $this->travelRequest->departure_date->toDateString(),
            'return_date' => $this->travelRequest->return_date->toDateString(),
            'old_status' => $this->travelRequest->getOriginal('status'),
            'new_status' => $this->newStatus,
            'message' => "Seu pedido de viagem para {$this->travelRequest->destination} foi {$this->getStatusMessage($this->newStatus)}."
        ];
    }

    private function getStatusMessage(string $status): string
    {
        return match($status) {
            'approved' => 'aprovado',
            'cancelled' => 'cancelado',
            'requested' => 'solicitado',
            default => $status
        };
    }
}