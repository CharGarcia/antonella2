<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Chatbot extends Component
{
    public $mostrarChat = false;
    public $messages = [];
    public $input = '';

    public function toggleChat()
    {
        $this->mostrarChat = !$this->mostrarChat;
    }

    public function sendMessage()
    {
        if (trim($this->input) === '') return;

        $this->messages[] = ['role' => 'user', 'content' => $this->input];

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => array_merge([
                ['role' => 'system', 'content' => 'Eres un asistente para un sistema contable SaaS llamado Camagare. Ayuda al usuario con preguntas comunes de uso.'],
            ], $this->messages),
            'temperature' => 0.7,
        ]);

        $reply = $response['choices'][0]['message']['content'] ?? 'Lo siento, no pude responder eso.';

        $this->messages[] = ['role' => 'assistant', 'content' => $reply];
        $this->input = '';
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
