<?php

namespace App\Interfaces;

use App\Models\TravelRequest;

interface NotificationServiceInterface
{
    public function sendStatusUpdateNotification(TravelRequest $travelRequest, string $newStatus): void;
    public function sendNewRequestNotification(TravelRequest $travelRequest): void;
    public function sendTravelReminderNotification(TravelRequest $travelRequest): void;
}