<div style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    {{-- Botón flotante --}}
   <button wire:click="toggleChat"
        class="btn btn-primary rounded-circle p-3 shadow"
        style="width: 60px; height: 60px; font-size: 20px;">
        @if($mostrarChat)
            ✖
        @else
            💬
        @endif
    </button>

    {{-- Ventana de chat --}}
    @if($mostrarChat)
        <div class="card mt-2 shadow" style="width: 320px;">
            <div class="card-header bg-primary text-white p-2 d-flex justify-content-between align-items-center">
                <span>
    <i class="fas fa-comments me-2"></i> Asistente CaMaGaRe
</span>
                {{-- <button wire:click="toggleChat" class="btn btn-sm btn-light">✖</button> --}}
            </div>

            <div class="card-body" style="height: 300px; overflow-y: auto; font-size: 0.9rem;">
                @foreach($messages as $message)
                    <div class="mb-2 text-{{ $message['role'] === 'user' ? 'end' : 'start' }}">
                        <span class="badge bg-{{ $message['role'] === 'user' ? 'info' : 'secondary' }}">
                            {{ $message['content'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="card-footer p-2">
                <div class="input-group">
                    <input wire:model="input"
                           wire:keydown.enter="sendMessage"
                           class="form-control form-control-sm"
                           placeholder="Escribe tu pregunta...">
                    <button wire:click="sendMessage" class="btn btn-sm btn-success">Enviar</button>
                </div>
            </div>
        </div>
    @endif
</div>

