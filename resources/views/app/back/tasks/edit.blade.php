@extends('layouts.admin')

@section('title', 'Editar Tarea')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Tarea</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Proyecto -->
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Proyecto *</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                        {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título de la Tarea *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Asignar a -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asignar a (Checklist y Búsqueda)</label>
                                
                                <div class="card p-2 bg-light border">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-white p-1"><i class="fas fa-search small"></i></span>
                                        <input type="text" id="assignee_search" class="form-control form-control-sm" placeholder="Buscar..." onkeyup="filterAssignees()">
                                    </div>

                                    <div id="assignee_list" style="max-height: 150px; overflow-y: auto;" class="bg-white border rounded p-2">
                                        @foreach($users as $user)
                                            <div class="form-check assignee-item">
                                                <input class="form-check-input" type="checkbox" name="assignees[]" value="{{ $user->id }}" 
                                                       id="assignee_{{ $user->id }}"
                                                       {{ (collect(old('assignees', $task->assignees->pluck('id')))->contains($user->id)) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 small" for="assignee_{{ $user->id }}">
                                                    {{ $user->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('assignees')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <script>
                            function filterAssignees() {
                                const input = document.getElementById('assignee_search');
                                const filter = input.value.toLowerCase();
                                const list = document.getElementById('assignee_list');
                                const items = list.getElementsByClassName('assignee-item');

                                for (let i = 0; i < items.length; i++) {
                                    const label = items[i].getElementsByTagName('label')[0];
                                    const txtValue = label.textContent || label.innerText;
                                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                        items[i].style.display = "";
                                    } else {
                                        items[i].style.display = "none";
                                    }
                                }
                            }
                            </script>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Estado *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="pendiente" {{ old('status', $task->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_progreso" {{ old('status', $task->status) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completada" {{ old('status', $task->status) == 'completada' ? 'selected' : '' }}>Completada</option>
                                    <option value="atrasada" {{ old('status', $task->status) == 'atrasada' ? 'selected' : '' }}>Atrasada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Prioridad -->
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Prioridad *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority">
                                    <option value="">Seleccione una prioridad...</option>
                                    <option value="baja" {{ old('priority', $task->priority) == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('priority', $task->priority) == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('priority', $task->priority) == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Límite -->
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Fecha Límite *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
