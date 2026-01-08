<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InnovationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceTypeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\TemporaryUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app.front.welcome');
});

// Ruta DEBUG temporal para arreglar el tema del correo
Route::get('/debug-url', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return response()->json([
        'config_app_url' => config('app.url'),
        'request_url' => request()->url(),
        'request_scheme' => request()->getScheme(),
        'is_secure' => request()->secure(),
        'trust_proxies' => request()->getTrustedProxies(),
        'headers' => request()->header(),
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    // Route for "Live Verification" polling
    Route::get('/email/verification-status', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'verified' => $request->user()->hasVerifiedEmail(),
        ]);
    })->name('verification.status');

    // Profile routes (no verified required so user can fix email if needed)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // User Management routes (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
        Route::post('users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');
        Route::post('users/{user}/manual-reset', [\App\Http\Controllers\UserController::class, 'manualPasswordReset'])->name('users.manualReset');
    });

    // Public User Profiles
    Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    
    // Project routes
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/upload-report', [ProjectController::class, 'uploadFinalReport'])->name('projects.uploadReport');
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Resource routes
    Route::resource('resources', ResourceController::class);
    Route::get('resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::post('resource-types', [ResourceTypeController::class, 'store'])->name('resource-types.store');
    Route::delete('resource-types/{resourceType}', [ResourceTypeController::class, 'destroy'])->name('resource-types.destroy');
    Route::post('projects/{project}/resources', [ResourceController::class, 'assignToProject'])->name('projects.resources.assign');
    Route::delete('projects/{project}/resources/{resource}', [ResourceController::class, 'removeFromProject'])->name('projects.resources.remove');
    
    // Chat routes
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{user}', [ChatController::class, 'store'])->name('chat.store');

    // Forum routes
    Route::resource('forum', ForumController::class);
    Route::post('forum/{topic}/posts', [ForumController::class, 'storePost'])->name('forum.storePost');

    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    
    // Innovation routes 
    Route::get('innovations/best-practices', [InnovationController::class, 'bestPractices'])->name('innovations.best-practices');
    Route::resource('innovations', InnovationController::class);
    Route::delete('innovations/{innovation}/attachments/{attachment}', [InnovationController::class, 'deleteAttachment'])
        ->name('innovations.attachments.delete');
    Route::post('innovations/{innovation}/request-review', [InnovationController::class, 'requestReview'])
        ->name('innovations.request-review');
    Route::post('innovations/{innovation}/approve', [InnovationController::class, 'approve'])
        ->name('innovations.approve')
        ->middleware('role:admin');
    Route::post('innovations/{innovation}/reject', [InnovationController::class, 'reject'])
        ->name('innovations.reject')
        ->middleware('role:admin');
    
    // Innovation types management (Admin/Coordinator)
    Route::get('innovation-types', [\App\Http\Controllers\InnovationTypeController::class, 'index'])->name('innovation-types.index');
    Route::post('innovation-types', [\App\Http\Controllers\InnovationTypeController::class, 'store'])->name('innovation-types.store');
    Route::put('innovation-types/{innovationType}', [\App\Http\Controllers\InnovationTypeController::class, 'update'])->name('innovation-types.update');
    Route::delete('innovation-types/{innovationType}', [\App\Http\Controllers\InnovationTypeController::class, 'destroy'])->name('innovation-types.destroy');
    
    // Report routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/projects/pdf', [ReportController::class, 'projectsPdf'])->name('reports.projects.pdf');
    Route::get('reports/projects/excel', [ReportController::class, 'projectsExcel'])->name('reports.projects.excel');
    Route::get('reports/tasks/pdf', [ReportController::class, 'tasksPdf'])->name('reports.tasks.pdf');
    Route::get('reports/tasks/excel', [ReportController::class, 'tasksExcel'])->name('reports.tasks.excel');
    Route::get('reports/innovations/pdf', [ReportController::class, 'innovationsPdf'])->name('reports.innovations.pdf');
    Route::get('reports/innovations/excel', [ReportController::class, 'innovationsExcel'])->name('reports.innovations.excel');
    Route::get('reports/participation', [ReportController::class, 'teacherParticipation'])->name('reports.participation');
    Route::get('reports/comparative', [ReportController::class, 'comparative'])->name('reports.comparative');
    Route::get('reports/comparative/pdf', [ReportController::class, 'comparativePdf'])->name('reports.comparative.pdf');
    Route::get('reports/comparative/excel', [ReportController::class, 'comparativeExcel'])->name('reports.comparative.excel');
    
    // Evaluation routes
    Route::get('projects/{project}/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('projects/{project}/evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::put('evaluations/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update');
    Route::delete('evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');
    Route::get('fix-storage-link', [EvaluationController::class, 'fixStorage'])->name('storage.fix');
    
    // Comment routes
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Notification routes
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications/read/all', [App\Http\Controllers\NotificationController::class, 'destroyAllRead'])->name('notifications.destroyAllRead');

    // Meeting routes
    Route::resource('meetings', \App\Http\Controllers\MeetingController::class);
    Route::post('meetings/{meeting}/attendance', [\App\Http\Controllers\MeetingController::class, 'updateAttendance'])->name('meetings.updateAttendance');
    Route::post('meetings/{meeting}/complete', [\App\Http\Controllers\MeetingController::class, 'complete'])->name('meetings.complete');
    Route::post('meetings/{meeting}/cancel', [\App\Http\Controllers\MeetingController::class, 'cancel'])->name('meetings.cancel');
    Route::post('meetings/{meeting}/reminders', [\App\Http\Controllers\MeetingController::class, 'sendReminders'])->name('meetings.sendReminders');

    // Calendar routes
    Route::get('calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [\App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');
    Route::get('calendar/export', [\App\Http\Controllers\CalendarController::class, 'exportIcs'])->name('calendar.export');

    // Attachment routes
    Route::post('attachments/{type}/{id}', [\App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [\App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Temporary upload routes
    Route::post('temp-upload', [TemporaryUploadController::class, 'store'])->name('temp.upload');
    Route::delete('temp-delete', [TemporaryUploadController::class, 'destroy'])->name('temp.delete');

    // Route for safe previewing (avoids 403 direct access issues)
    Route::get('storage-preview/{path}', [\App\Http\Controllers\AttachmentController::class, 'preview'])
        ->where('path', '.*')
        ->name('storage.preview');
});

require __DIR__.'/auth.php';
