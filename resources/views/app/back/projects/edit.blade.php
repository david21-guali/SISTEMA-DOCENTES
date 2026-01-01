@extends('layouts.admin')

@section('title', 'Editar Proyecto')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Proyecto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título del Proyecto *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $project->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categoría *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Equipo de Trabajo -->
                        <div class="mb-3">
                            <label class="form-label">Equipo de Trabajo (Checklist y Búsqueda)</label>
                            
                            <div class="card p-2 bg-light border">
                                <!-- Search Input -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                    <input type="text" id="user_search" class="form-control" placeholder="Buscar persona..." onkeyup="filterUsers()">
                                </div>

                                <!-- Checklist Container -->
                                <div id="user_list" style="max-height: 200px; overflow-y: auto;" class="bg-white border rounded p-2">
                                    @foreach($users as $user)
                                        <div class="form-check user-item">
                                            <input class="form-check-input" type="checkbox" name="team_members[]" value="{{ $user->id }}" 
                                                   id="user_{{ $user->id }}"
                                                   {{ (collect(old('team_members', $project->team->pluck('id')))->contains($user->id)) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="user_{{ $user->id }}">
                                                {{ $user->name }} <span class="text-muted small">({{ $user->email }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Selecciona los participantes del equipo.</small>
                            @error('team_members')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                        function filterUsers() {
                            const input = document.getElementById('user_search');
                            const filter = input.value.toLowerCase();
                            const list = document.getElementById('user_list');
                            const items = list.getElementsByClassName('user-item');

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

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Objetivos -->
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" 
                                      id="objectives" name="objectives" rows="3">{{ old('objectives', $project->objectives) }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Fecha Inicio -->
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!--  Estado -->
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Estado *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="planificacion" {{ old('status', $project->status) == 'planificacion' ? 'selected' : '' }}>Planificación</option>
                                    <option value="en_progreso" {{ old('status', $project->status) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="finalizado" {{ old('status', $project->status) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    <option value="en_riesgo" {{ old('status', $project->status) == 'en_riesgo' ? 'selected' : '' }}>En Riesgo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Avance -->
                            <div class="col-md-3 mb-3">
                                <label for="completion_percentage" class="form-label">Avance (%)</label>
                                <input type="number" class="form-control bg-light" 
                                       id="completion_percentage" name="completion_percentage" 
                                       value="{{ old('completion_percentage', $project->completion_percentage) }}" readonly>
                                <small class="text-muted">Calculado automáticamente por tareas</small>
                            </div>
                        </div>

                        <!-- Opciones Financieras -->
                        <div class="mb-3 border p-3 rounded bg-light">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="project_needs_budget" 
                                        {{ (old('budget', $project->budget) > 0) ? 'checked' : '' }}
                                        onchange="toggleBudget()">
                                <label class="form-check-label fw-bold" for="project_needs_budget">
                                    <i class="fas fa-coins"></i> ¿Este proyecto requiere adquisición de bienes o presupuesto?
                                </label>
                            </div>
                            
                            <!-- Presupuesto (Oculto por defecto) -->
                            <div class="mt-3" id="budget_container" style="display: {{ (old('budget', $project->budget) > 0) ? 'block' : 'none' }};">
                                <label for="budget" class="form-label">Monto del Presupuesto ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" max="9999999.99"
                                            class="form-control @error('budget') is-invalid @enderror" 
                                            id="budget" name="budget" value="{{ old('budget', $project->budget) }}"
                                            placeholder="0.00" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                </div>
                                <small class="text-muted">Ingrese el valor total (Máx 7 dígitos).</small>
                                @error('budget')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <script>
                        function toggleBudget() {
                            const checkBox = document.getElementById('project_needs_budget');
                            const container = document.getElementById('budget_container');
                            const input = document.getElementById('budget');
                            
                            if (checkBox.checked) {
                                container.style.display = 'block';
                                input.focus();
                            } else {
                                container.style.display = 'none';
                                input.value = ''; // Limpiar si se desmarca
                            }
                        }
                        </script>

                        <!-- Descripción de Impacto -->
                        <div class="mb-3">
                            <label for="impact_description" class="form-label">Descripción del Impacto</label>
                            <textarea class="form-control @error('impact_description') is-invalid @enderror" 
                                      id="impact_description" name="impact_description" rows="2">{{ old('impact_description', $project->impact_description) }}</textarea>
                            @error('impact_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Proyecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
