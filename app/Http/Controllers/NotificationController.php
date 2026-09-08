<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Mark a single notification as read (optional).
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        // Redirect to the related incident if possible
        $incidentId = $notification->data['incident_id'] ?? null;

        if ($incidentId) {
            return redirect()
                ->route('incidents.show', $incidentId)
                ->with('success', 'Notification marquée comme lue.');
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }
}
