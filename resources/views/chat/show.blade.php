@extends('layouts.admin')

@section('title', 'Chat con ' . $user->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/chat.css') }}">
@endpush
@section('contenido')
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-md-12">
            <div class="card shadow chat-main-card">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm me-3"><i class="fas fa-arrow-left"></i></a>
                        <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0 chat-avatar-md">
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
                            <div class="card chat-message-bubble {{ $message->sender_id == Auth::user()->profile->id ? 'bg-primary text-white chat-message-out' : 'bg-white chat-message-in' }}">
                                <div class="card-body p-2 px-3">
                                    <p class="mb-1">{{ $message->content }}</p>
                                    <div class="d-flex align-items-center {{ $message->sender_id == Auth::user()->profile->id ? 'justify-content-end' : 'justify-content-start' }}">
                                        <small class="chat-time {{ $message->sender_id == Auth::user()->profile->id ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </small>
                                        @if($message->sender_id == Auth::user()->profile->id)
                                            <i class="fas fa-check-double ms-1 chat-check-icon {{ $message->read_at ? 'text-white' : 'text-white-50' }}"></i>
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
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection

@endsection
