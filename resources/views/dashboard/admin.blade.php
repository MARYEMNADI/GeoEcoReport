<x-app-layout>
    <x-slot:header>
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Vue d'ensemble de la plateforme
                </h2>
            </div>
            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">
                Administrateur
            </span>
        </div>
    </x-slot:header>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ========== KPI CARDS ========== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total incidents</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_incidents'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Utilisateurs</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Techniciens</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_techniciens'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Taux de résolution</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['taux_resolution'] }}%</p>
            </div>
        </div>

        {{-- ========== STATUTS + CATÉGORIES ========== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Incidents par statut --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-4">Incidents par statut</h3>
                <div class="space-y-3">
                    @foreach($byStatus as $status => $count)
                        @php
                            $colors = [
                                'En attente'             => 'bg-yellow-400',
                                'En cours de traitement' => 'bg-blue-500',
                                'Résolu'                 => 'bg-green-500',
                                'Rejeté'                 => 'bg-red-400',
                            ];
                            $percent = $stats['total_incidents'] > 0
                                ? round(($count / $stats['total_incidents']) * 100)
                                : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $status }}</span>
                                <span class="font-medium text-gray-800">{{ $count }} ({{ $percent }}%)</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $colors[$status] ?? 'bg-gray-400' }} h-2 rounded-full"
                                     style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Par catégorie --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-4">Par catégorie</h3>
                <div class="space-y-3">
                    @foreach($byCategory as $item)
                        @php
                            $percent = $stats['total_incidents'] > 0
                                ? round(($item->total / $stats['total_incidents']) * 100)
                                : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $item->name }}</span>
                                <span class="font-medium text-gray-800">{{ $percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========== INCIDENTS EN ATTENTE ========== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Incidents en attente d'affectation</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Incident</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pendingIncidents as $incident)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('incidents.show', $incident) }}"
                                       class="font-medium text-gray-900 hover:text-green-600">
                                        {{ $incident->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $incident->category->name ?? '' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        {{ $incident->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'Urgente' => 'bg-red-100 text-red-700',
                                            'Élevée'  => 'bg-orange-100 text-orange-700',
                                            'Moyenne' => 'bg-yellow-100 text-yellow-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $incident->priority ?? 'Moyenne' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('incidents.show', $incident) }}"
                                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Affecter →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                    Aucun incident en attente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>