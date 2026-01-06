<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * List all notifications for the authenticated user.
     */
    public function index(): \Illuminate\View\View
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id): \Illuminate\Http\RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Redirect to the target link if available
            if (isset($notification->data['link'])) {
                return redirect($notification->data['link']);
            }
            
            // Try to determine the link based on available data
            $data = $notification->data;
            
            // For meetings
            if (isset($data['meeting_id'])) {
                return redirect()->route('meetings.show', $data['meeting_id']);
            }
            
            // For tasks
            if (isset($data['task_id'])) {
                return redirect()->route('tasks.show', $data['task_id']);
            }
            
            // For projects
            if (isset($data['project_id'])) {
                return redirect()->route('projects.show', $data['project_id']);
            }
            
            // For comments (redirect to project)
            if (isset($data['comment_id']) && isset($data['project_id'])) {
                return redirect()->route('projects.show', $data['project_id']);
            }
        }

        return back()->with('info', 'Notificación marcada como leída.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): \Illuminate\Http\RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
    /**
     * Remove the specified notification.
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notificación eliminada.');
        }

        return back()->with('error', 'Notificación no encontrada.');
    }

    /**
     * Remove all read notifications.
     */
    public function destroyAllRead(): \Illuminate\Http\RedirectResponse
    {
        Auth::user()->readNotifications()->delete();
        return back()->with('success', 'Todas las notificaciones leídas han sido eliminadas.');
    }
}
