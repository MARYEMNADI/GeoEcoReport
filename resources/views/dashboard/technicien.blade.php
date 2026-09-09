<x-app-layout>
    <x-slot:header>
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tableau de bord technicien
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Vos interventions et incidents affectés
                </p>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                Technicien
            </span>
        </div>
    </x-slot:header>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ========== CARDS ========== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Incidents affectés</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">En attente</p>
                <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $stats['en_attente'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">En cours</p>
                <p class="text-3xl font-bold text-blue-500 mt-1">{{ $stats['en_cours'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Résolus</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['resolus'] }}</p>
            </div>
        </div>

        {{-- ========== TABLEAU ========== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Mes interventions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Incident</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signalé par</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($incidents as $incident)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('incidents.show', $incident) }}"
                                       class="font-medium text-gray-900 hover:text-green-600">
                                        {{ $incident->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $incident->category->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'Urgente' => 'bg-red-100 text-red-700',
                                            'Élevée'  => 'bg-orange-100 text-orange-700',
                                            'Moyenne' => 'bg-yellow-100 text-yellow-700',
                                            'Faible'  => 'bg-blue-100 text-blue-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $incident->priority ?? 'Moyenne' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'En attente'             => 'bg-yellow-100 text-yellow-700',
                                            'En cours de traitement' => 'bg-blue-100 text-blue-700',
                                            'Résolu'                 => 'bg-green-100 text-green-700',
                                            'Rejeté'                 => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $incident->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $incident->user->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('incidents.show', $incident) }}"
                                       class="text-sm text-green-600 hover:text-green-800 font-medium">
                                        Traiter →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Aucun incident ne vous a été affecté pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>