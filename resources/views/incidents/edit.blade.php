<x-app-layout>
    <x-slot:header>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $incident->title }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Signalé le {{ $incident->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('incidents.index') }}"
                   class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    ← Liste
                </a>
                @can('update', $incident)
                    <a href="{{ route('incidents.edit', $incident) }}"
                       class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Modifier
                    </a>
                @endcan
            </div>
        </div>
    </x-slot:header>

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Infos principales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $incident->description }}</p>
                </div>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Statut :</span>
                        <span class="ml-2 font-medium">{{ $incident->status }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Priorité :</span>
                        <span class="ml-2 font-medium">{{ $incident->priority ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Catégorie :</span>
                        <span class="ml-2 font-medium">{{ $incident->category->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Signalé par :</span>
                        <span class="ml-2 font-medium">{{ $incident->user->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Localisation :</span>
                        <span class="ml-2 font-medium">{{ $incident->latitude }}, {{ $incident->longitude }}</span>
                    </div>
                </div>
            </div>

            {{-- AI Summary --}}
            @if($incident->ai_summary || $incident->ai_suggested_category)
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-green-700 mb-2">🤖 Analyse Assistant</h3>
                    @if($incident->ai_summary)
                        <p class="text-sm text-gray-700 mb-1">{{ $incident->ai_summary }}</p>
                    @endif
                    @if($incident->ai_suggested_category)
                        <p class="text-sm text-gray-600">Catégorie suggérée : <strong>{{ $incident->ai_suggested_category }}</strong></p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Photos --}}
        @if($incident->images->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Photos</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($incident->images as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Photo" class="w-full h-40 object-cover rounded-lg">
                            @can('update', $incident)
                                <form action="{{ route('incidents.images.destroy', [$incident, $image]) }}"
                                      method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition"
                                      onsubmit="return confirm('Supprimer cette photo ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white text-xs px-2 py-1 rounded">
                                        Supprimer
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Changer le statut --}}
        @can('changeStatus', $incident)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Changer le statut</h3>
                <form action="{{ route('incidents.status.update', $incident) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    @method('PATCH')
                    <div>
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                            @foreach(['En attente', 'En cours de traitement', 'Résolu', 'Rejeté'] as $status)
                                <option value="{{ $status }}" @selected($incident->status === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Mettre à jour
                    </button>
                </form>
            </div>
        @endcan

        {{-- Affectation --}}
        @can('assign', $incident)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Affecter à un technicien</h3>
                <form action="{{ route('incidents.assign', $incident) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Technicien</label>
                        <select name="technicien_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Choisir un technicien</option>
                            @foreach($techniciens as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructions (optionnel)</label>
                        <textarea name="instructions" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Affecter
                    </button>
                </form>
            </div>
        @endcan

        {{-- Affectations existantes --}}
        @if($incident->affectations->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Techniciens affectés</h3>
                <ul class="space-y-2">
                    @foreach($incident->affectations as $aff)
                        <li class="flex justify-between items-center text-sm">
                            <span>{{ $aff->technicien->name ?? '—' }}</span>
                            <span class="text-gray-500">{{ $aff->date_affectation?->format('d/m/Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Commentaires --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Commentaires</h3>

            <div class="space-y-4 mb-6">
                @forelse($incident->comments as $comment)
                    <div class="border-b border-gray-100 pb-3">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-800">{{ $comment->user->name ?? '—' }}</span>
                            <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700 mt-1">{{ $comment->content }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Aucun commentaire pour le moment.</p>
                @endforelse
            </div>

            <form action="{{ route('comments.store', $incident) }}" method="POST">
                @csrf
                <textarea name="content" rows="3" required placeholder="Ajouter un commentaire..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 mb-3"></textarea>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Publier
                </button>
            </form>
        </div>

        {{-- Lien Assistant --}}
        <div class="text-center">
            <a href="{{ route('assistant.index', ['incident_id' => $incident->id]) }}"
               class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 text-sm font-medium">
                🤖 Demander à l'Assistant GeoEco
            </a>
        </div>
    </div>
</x-app-layout>