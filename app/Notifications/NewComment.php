<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IncidentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Incident $incident,
        public string $oldStatus,
        public string $newStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'incident_status_changed',
            'incident_id' => $this->incident->id,
            'title'       => $this->incident->title,
            'old_status'  => $this->oldStatus,
            'new_status'  => $this->newStatus,
            'message'     => "Le statut de votre incident a changé : {$this->newStatus}.",
        ];
    }
}