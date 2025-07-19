<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class NotificationService implements NotificationServiceInterface
{
    public function sendStatusUpdateNotification(TravelRequest $travelRequest, string $newStatus): void
    {
        try {
            $user = $travelRequest->user;
            
            if (! $user) {
                Log::warning("Usuário não encontrado para o pedido de viagem {$travelRequest->id}");
                return;
            }

            $user->notify(new TravelRequestStatusUpdated($travelRequest, $newStatus));
            
            Log::info("Notificação enviada para usuário {$user->id} sobre atualização do pedido {$travelRequest->id} para status: {$newStatus}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de atualização de status: {$e->getMessage()}", [
                'travel_request_id' => $travelRequest->id,
                'new_status' => $newStatus,
                'exception' => $e
            ]);
        }
    }

    public function sendNewRequestNotification(TravelRequest $travelRequest): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            
            if ($admins->isEmpty()) {
                Log::warning("Nenhum administrador encontrado para notificar sobre novo pedido {$travelRequest->id}");
                return;
            }

            Notification::send($admins, new \App\Notifications\NewTravelRequestCreated($travelRequest));
            
            Log::info("Notificação de novo pedido enviada para " . $admins->count() . " administrador(es) sobre o pedido {$travelRequest->id}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de novo pedido: {$e->getMessage()}", [
                'travel_request_id' => $travelRequest->id,
                'exception' => $e
            ]);
        }
    }

    public function sendTravelReminderNotification(TravelRequest $travelRequest): void
    {
        try {
            $user = $travelRequest->user;
            
            if (!$user) {
                Log::warning("Usuário não encontrado para lembrete do pedido de viagem {$travelRequest->id}");
                return;
            }

            $user->notify(new \App\Notifications\TravelReminder($travelRequest));
            
            Log::info("Lembrete de viagem enviado para usuário {$user->id} sobre o pedido {$travelRequest->id}");
            
        } catch (\Exception $e) {
            Log::error("Erro ao enviar lembrete de viagem: {$e->getMessage()}", [
                'travel_request_id' => $travelRequest->id,
                'exception' => $e
            ]);
        }
    }
}