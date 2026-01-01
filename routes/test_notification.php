<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskAssigned;

Route::get('/test-notification', function () {
    $user = auth()->user();
    
    // Crear una tarea dummy en memoria (no guardada en DB si no queremos, pero para la notificación necesitamos ID posiblemente)
    // Mejor creamos una instancia fake manual
    $task = new Task([
        'id' => 99999,
        'title' => 'Tarea de Prueba de Notificación',
        'project_id' => 1,
        'status' => 'pendiente'
    ]);
    
    try {
        $user->notify(new TaskAssigned($task));
        return "Notificación enviada a " . $user->email . ". <br>Revisa la campana, deberías ver un número rojo. <a href='/dashboard'>Volver al Dashboard</a>";
    } catch (\Exception $e) {
        return "Error enviando notificación: " . $e->getMessage();
    }
})->middleware('auth');
