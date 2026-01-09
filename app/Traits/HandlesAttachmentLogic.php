<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\Task;

trait HandlesAttachmentLogic
{
    /**
     * Resolve the attachable model instance.
     * 
     * @param string $type
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getAttachable(string $type, int $id): \Illuminate\Database\Eloquent\Model
    {
        $models = ['project' => Project::class, 'task' => Task::class];
        return ($models[$type] ?? abort(404, 'Tipo no válido'))::findOrFail($id);
    }

    /**
     * Format the response for attachment actions.
     * 
     * @param string $message
     * @param array<string, mixed> $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function formatResponse(string $message, array $data = [], int $status = 200)
    {
        if (request()->expectsJson()) {
            return response()->json(array_merge(['success' => $status < 400, 'message' => $message], $data), $status);
        }

        return back()->with($status >= 400 ? 'error' : 'success', $message);
    }
}
