<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IncidentAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public Incident $incident
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'incident_assigned',
            'incident_id' => $this->incident->id,
            'title' => $this->incident->title,
            'message' => 'Un incident vous a été affecté.',
        ];
    }
}