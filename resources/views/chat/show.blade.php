@extends('layouts.admin')

@section('title', 'Chat con ' . $user->name)

@section('contenido')
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-md-12">
            <div class="card shadow" style="height: calc(100vh - 150px);">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm me-3"><i class="fas fa-arrow-left"></i></a>
                        <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="mb-0 text-truncate">{{ $user->name }}</h5>
                    </div>
                </div>

                <div class="card-body overflow-auto bg-light" id="chatBox">
                    @forelse($messages as $message)
                        <div class="d-flex mb-3 {{ $message->sender_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="card {{ $message->sender_id == Auth::id() ? 'bg-primary text-white' : 'bg-white' }}" style="max-width: 70%;">
                                <div class="card-body p-2 px-3">
                                    <p class="mb-1">{{ $message->content }}</p>
                                    <small class="{{ $message->sender_id == Auth::id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.75rem;">
                                        {{ $message->created_at->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-5">
                            <small>No hay mensajes previos. ¡Saluda!</small>
                        </div>
                    @endforelse
                </div>

                <div class="card-footer bg-white">
                    <form action="{{ route('chat.store', $user->id) }}" method="POST" class="d-flex">
                        @csrf
                        <input type="text" name="content" class="form-control me-2 @error('content') is-invalid @enderror" placeholder="Escribe un mensaje..." value="{{ old('content') }}" autocomplete="off">
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Scroll al fondo
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endsection

@endsection
