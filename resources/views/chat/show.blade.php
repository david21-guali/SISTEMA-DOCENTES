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
                    @php $lastDate = null; @endphp
                    @forelse($messages as $message)
                        @php 
                            $messageDate = $message->created_at->translatedFormat('d M, Y');
                            $today = now()->translatedFormat('d M, Y');
                            $yesterday = now()->subDay()->translatedFormat('d M, Y');
                            
                            $displayDate = $messageDate;
                            if($messageDate == $today) $displayDate = 'Hoy';
                            elseif($messageDate == $yesterday) $displayDate = 'Ayer';
                        @endphp

                        @if($lastDate !== $messageDate)
                            <div class="text-center my-4">
                                <span class="badge bg-white text-muted border px-3 py-2 rounded-pill small fw-normal shadow-sm">
                                    <i class="fas fa-calendar-alt me-1 opacity-50"></i> {{ $displayDate }}
                                </span>
                            </div>
                            @php $lastDate = $messageDate; @endphp
                        @endif

                        <div class="d-flex mb-3 {{ $message->sender_id == Auth::user()->profile->id ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="card {{ $message->sender_id == Auth::user()->profile->id ? 'bg-primary text-white border-0 shadow-sm' : 'bg-white border-0 shadow-sm' }}" style="max-width: 70%; border-radius: 15px; {{ $message->sender_id == Auth::user()->profile->id ? 'border-bottom-right-radius: 2px;' : 'border-bottom-left-radius: 2px;' }}">
                                <div class="card-body p-2 px-3">
                                    <p class="mb-1">{{ $message->content }}</p>
                                    <div class="d-flex align-items-center {{ $message->sender_id == Auth::user()->profile->id ? 'justify-content-end' : 'justify-content-start' }}">
                                        <small class="{{ $message->sender_id == Auth::user()->profile->id ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.7rem;">
                                            {{ $message->created_at->format('H:i') }}
                                        </small>
                                        @if($message->sender_id == Auth::user()->profile->id)
                                            <i class="fas fa-check-double ms-1 {{ $message->read_at ? 'text-white' : 'text-white-50' }}" style="font-size: 0.6rem;"></i>
                                        @endif
                                    </div>
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
