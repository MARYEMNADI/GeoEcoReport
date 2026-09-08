<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Assistant GeoEco
        </h2>
    </x-slot:header>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        {{-- Incident context (si présent) --}}
        @if($incident)
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-sm text-green-800">
                    <span class="font-semibold">Contexte :</span>
                    Incident #{{ $incident->id }} — {{ $incident->title }}
                </p>
                <a href="{{ route('incidents.show', $incident) }}" class="text-xs text-green-600 hover:underline mt-1 inline-block">
                    Voir l'incident →
                </a>
            </div>
        @endif

        {{-- Chat box --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Messages --}}
            <div class="h-[480px] overflow-y-auto p-6 space-y-4" id="chat-messages">

                @if(empty($history))
                    <div class="text-center text-gray-500 py-12">
                        <div class="text-4xl mb-3">🤖</div>
                        <p class="font-medium">Bonjour ! Je suis l'assistant GeoEcoReport.</p>
                        <p class="text-sm mt-1">Posez-moi une question ou demandez une analyse d'incident.</p>
                    </div>
                @else
                    @foreach($history as $message)
                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm
                                {{ $message['role'] === 'user'
                                    ? 'bg-green-600 text-white rounded-br-none'
                                    : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                                {!! nl2br(e($message['content'])) !!}
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

            {{-- Input --}}
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <form action="{{ route('assistant.ask') }}" method="POST" class="flex gap-3">
                    @csrf

                    @if($incident)
                        <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                    @endif

                    <input
                        type="text"
                        name="question"
                        value="{{ old('question') }}"
                        placeholder="Écrivez votre question..."
                        class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                        required
                        autofocus
                    >

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition"
                    >
                        Envoyer
                    </button>
                </form>

                @error('question')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-4 flex justify-between items-center text-sm">
            <form action="{{ route('assistant.clear') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-red-600">
                    Effacer la conversation
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="text-green-600 hover:underline">
                ← Retour au tableau de bord
            </a>
        </div>

    </div>

    <script>
        // Auto-scroll to bottom
        const chat = document.getElementById('chat-messages');
        if (chat) {
            chat.scrollTop = chat.scrollHeight;
        }
    </script>
</x-app-layout>