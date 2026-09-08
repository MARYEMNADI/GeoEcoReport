<x-app-layout>
    <x-slot:header>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Liste des Incidents Écologiques
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Gestion et suivi des signalements écologiques
                </p>
            </div>

            @can('create', App\Models\Incident::class)
                <a href="{{ route('incidents.create') }}"
                   class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition">
                    + Signaler un incident
                </a>
            @endcan
        </div>
    </x-slot:header>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        {{-- Tableau --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre & Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signaleur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($incidents as $incident)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- Photo --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($incident->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $incident->images->first()->image_path) }}"
                                             alt="Photo"
                                             class="h-12 w-12 object-cover rounded-md border">
                                    @else
                                        <div class="h-12 w-12 rounded-md bg-gray-100 border flex items-center justify-center text-xs text-gray-400">
                                            N/A
                                        </div>
                                    @endif
                                </td>

                                {{-- Titre + Catégorie --}}
                                <td class="px-6 py-4">
                                    <a href="{{ route('incidents.show', $incident) }}"
                                       class="text-sm font-semibold text-gray-900 hover:text-green-600">
                                        {{ $incident->title }}
                                    </a>
                                    <div class="text-xs text-green-600 font-medium mt-1">
                                        {{ $incident->category->name ?? 'Non spécifiée' }}
                                    </div>
                                </td>

                                {{-- Priorité --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityClasses = match($incident->priority) {
                                            'Urgente' => 'bg-red-100 text-red-800',
                                            'Élevée'  => 'bg-orange-100 text-orange-800',
                                            'Moyenne' => 'bg-yellow-100 text-yellow-800',
                                            'Faible'  => 'bg-blue-100 text-blue-800',
                                            default   => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClasses }}">
                                        {{ $incident->priority ?? 'Moyenne' }}
                                    </span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = match($incident->status) {
                                            'En attente'             => 'bg-yellow-100 text-yellow-800',
                                            'En cours de traitement' => 'bg-blue-100 text-blue-800',
                                            'Résolu'                 => 'bg-green-100 text-green-800',
                                            'Rejeté'                 => 'bg-red-100 text-red-800',
                                            default                  => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $incident->status ?? 'En attente' }}
                                    </span>
                                </td>

                                {{-- Signaleur --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $incident->user->name ?? 'Anonyme' }}
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $incident->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('incidents.show', $incident) }}"
                                           class="text-green-600 hover:text-green-900 font-bold">
                                            Voir
                                        </a>

                                        @can('update', $incident)
                                            <a href="{{ route('incidents.edit', $incident) }}"
                                               class="text-blue-600 hover:text-blue-900 font-bold">
                                                Modifier
                                            </a>
                                        @endcan

                                        @can('delete', $incident)
                                            <form action="{{ route('incidents.destroy', $incident) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 text-4xl mb-3">📋</div>
                                    <p class="text-gray-500 font-medium">Aucun incident signalé pour le moment.</p>
                                    @can('create', App\Models\Incident::class)
                                        <a href="{{ route('incidents.create') }}"
                                           class="inline-block mt-3 text-green-600 hover:text-green-800 font-semibold">
                                            + Signaler votre premier incident
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($incidents->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $incidents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>