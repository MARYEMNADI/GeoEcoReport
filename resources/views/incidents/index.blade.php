<x-app-layout>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <x-slot:header>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            {{-- Titre --}}
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>

                    <span class="text-xs font-semibold uppercase tracking-wider text-green-600">
                        Plateforme de supervision territoriale
                    </span>
                </div>

                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Liste des Incidents Écologiques
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Gestion et suivi en temps réel des signalements des citoyens
                </p>
            </div>

            {{-- Boutons --}}
            <div class="flex flex-wrap items-center gap-2">

                {{-- Retour Dashboard Admin --}}
                @if(auth()->user()->hasRole('administrateur'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
                        ← Vue d'ensemble de la plateforme
                    </a>
                @endif

                {{-- Signaler un incident --}}
                @can('create', App\Models\Incident::class)
                    <a href="{{ route('incidents.create') }}"
                       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">

                        <span class="text-lg">+</span>

                        <span>
                            Signaler un incident
                        </span>
                    </a>
                @endcan

            </div>

        </div>
    </x-slot:header>


    {{-- ========================================================= --}}
    {{-- CONTENT --}}
    {{-- ========================================================= --}}

    <div class="min-h-screen bg-gray-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">


            {{-- ================================================= --}}
            {{-- ALERT SUCCESS --}}
            {{-- ================================================= --}}

            @if(session('success'))

                <div class="mb-6 flex items-center justify-between gap-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-600">
                            ✓
                        </div>

                        <div>
                            <p class="text-sm font-semibold">
                                Opération réussie
                            </p>

                            <p class="text-sm">
                                {{ session('success') }}
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        onclick="this.parentElement.remove()"
                        class="text-green-500 hover:text-green-700 text-lg">
                        ×
                    </button>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- ALERT ERROR --}}
            {{-- ================================================= --}}

            @if(session('error'))

                <div class="mb-6 flex items-center justify-between gap-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600">
                            !
                        </div>

                        <div>
                            <p class="text-sm font-semibold">
                                Une erreur est survenue
                            </p>

                            <p class="text-sm">
                                {{ session('error') }}
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        onclick="this.parentElement.remove()"
                        class="text-red-500 hover:text-red-700 text-lg">
                        ×
                    </button>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- STATISTIQUES --}}
            {{-- ================================================= --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

                {{-- TOTAL --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Total signalements
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $totalIncidents }}
                    </p>

                    <p class="text-xs text-green-600 font-medium mt-1">
                        Tous les incidents
                    </p>

                </div>


                {{-- EN ATTENTE --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        En attente
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $pendingIncidents }}
                    </p>

                    <p class="text-xs text-orange-500 font-medium mt-1">
                        Action requise
                    </p>

                </div>


                {{-- EN COURS --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        En cours de traitement
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $inProgressIncidents }}
                    </p>

                    <p class="text-xs text-blue-600 font-medium mt-1">
                        Équipes mobilisées
                    </p>

                </div>


                {{-- RESOLUS --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Résolus
                    </p>

                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $resolvedIncidents }}
                    </p>

                    <p class="text-xs text-green-600 font-medium mt-1">
                        Taux : {{ $resolutionRate }}%
                    </p>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- FILTRES --}}
            {{-- ================================================= --}}

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-6">

                <form
                    method="GET"
                    action="{{ route('incidents.index') }}"
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3"
                >

                    {{-- Recherche --}}
                    <div class="xl:col-span-2">

                        <label class="sr-only">
                            Rechercher
                        </label>

                        <div class="relative">

                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                🔍
                            </span>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par titre, signaleur, lieu..."
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >

                        </div>

                    </div>


                    {{-- Catégorie --}}
                    <div>

                        <select
                            name="category_id"
                            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >

                            <option value="">
                                Toutes les catégories
                            </option>

                            @foreach(\App\Models\Category::orderBy('name')->get() as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(request('category_id') == $category->id)
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Statut --}}
                    <div>

                        <select
                            name="status"
                            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >

                            <option value="">
                                Tous les statuts
                            </option>

                            <option
                                value="En attente"
                                @selected(request('status') === 'En attente')
                            >
                                En attente
                            </option>

                            <option
                                value="En cours de traitement"
                                @selected(request('status') === 'En cours de traitement')
                            >
                                En cours de traitement
                            </option>

                            <option
                                value="Résolu"
                                @selected(request('status') === 'Résolu')
                            >
                                Résolu
                            </option>

                            <option
                                value="Rejeté"
                                @selected(request('status') === 'Rejeté')
                            >
                                Rejeté
                            </option>

                        </select>

                    </div>


                    {{-- Priorité --}}
                    <div>

                        <select
                            name="priority"
                            class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >

                            <option value="">
                                Toutes priorités
                            </option>

                            <option
                                value="Faible"
                                @selected(request('priority') === 'Faible')
                            >
                                Faible
                            </option>

                            <option
                                value="Moyenne"
                                @selected(request('priority') === 'Moyenne')
                            >
                                Moyenne
                            </option>

                            <option
                                value="Élevée"
                                @selected(request('priority') === 'Élevée')
                            >
                                Élevée
                            </option>

                            <option
                                value="Urgente"
                                @selected(request('priority') === 'Urgente')
                            >
                                Urgente
                            </option>

                        </select>

                    </div>


                    {{-- Boutons --}}
                    <div class="md:col-span-2 xl:col-span-5 flex flex-wrap items-center gap-2">

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2.5 rounded-lg transition"
                        >
                            🔍 Rechercher
                        </button>

                        <a
                            href="{{ route('incidents.index') }}"
                            class="inline-flex items-center justify-center gap-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-4 py-2.5 rounded-lg transition"
                        >
                            ↻ Réinitialiser
                        </a>

                    </div>

                </form>

            </div>


            {{-- ================================================= --}}
            {{-- TABLE --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div>

                        <h3 class="text-base font-bold text-gray-900">
                            Incidents signalés
                        </h3>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $incidents->total() }} incident(s) enregistré(s)
                        </p>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        {{-- HEADER --}}
                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Photo
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Titre & Catégorie
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Priorité
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Signaleur
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>

                                <th class="px-5 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        {{-- BODY --}}
                        <tbody class="bg-white divide-y divide-gray-100">

                            @forelse($incidents as $incident)

                                <tr class="hover:bg-green-50/30 transition">


                                    {{-- PHOTO --}}
                                    <td class="px-5 py-4 whitespace-nowrap">

                                        @if($incident->images->isNotEmpty())

                                            <img
                                                src="{{ asset('storage/' . $incident->images->first()->image_path) }}"
                                                alt="Photo de {{ $incident->title }}"
                                                class="h-12 w-12 object-cover rounded-lg border border-gray-200 shadow-sm"
                                            >

                                        @else

                                            <div class="h-12 w-12 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-xs text-gray-400">
                                                N/A
                                            </div>

                                        @endif

                                    </td>


                                    {{-- TITRE + CATÉGORIE --}}
                                    <td class="px-5 py-4">

                                        <a
                                            href="{{ route('incidents.show', $incident) }}"
                                            class="text-sm font-semibold text-gray-900 hover:text-green-600 transition"
                                        >
                                            {{ $incident->title }}
                                        </a>

                                        <div class="text-xs text-green-600 font-medium mt-1">
                                            {{ $incident->category->name ?? 'Non spécifiée' }}
                                        </div>

                                    </td>


                                    {{-- PRIORITÉ --}}
                                    <td class="px-5 py-4 whitespace-nowrap">

                                        @php
                                            $priorityClasses = match($incident->priority) {

                                                'Urgente' =>
                                                    'bg-red-50 text-red-700 border-red-200',

                                                'Élevée' =>
                                                    'bg-orange-50 text-orange-700 border-orange-200',

                                                'Moyenne' =>
                                                    'bg-yellow-50 text-yellow-700 border-yellow-200',

                                                'Faible' =>
                                                    'bg-blue-50 text-blue-700 border-blue-200',

                                                default =>
                                                    'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp

                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full border text-[11px] font-bold {{ $priorityClasses }}"
                                        >
                                            {{ $incident->priority ?? 'Moyenne' }}
                                        </span>

                                    </td>


                                    {{-- STATUT --}}
                                    <td class="px-5 py-4 whitespace-nowrap">

                                        @php
                                            $statusClasses = match($incident->status) {

                                                'En attente' =>
                                                    'bg-yellow-50 text-yellow-700 border-yellow-200',

                                                'En cours de traitement' =>
                                                    'bg-blue-50 text-blue-700 border-blue-200',

                                                'Résolu' =>
                                                    'bg-green-50 text-green-700 border-green-200',

                                                'Rejeté' =>
                                                    'bg-red-50 text-red-700 border-red-200',

                                                default =>
                                                    'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp

                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full border text-[11px] font-bold {{ $statusClasses }}"
                                        >
                                            {{ $incident->status ?? 'En attente' }}
                                        </span>

                                    </td>


                                    {{-- SIGNALEUR --}}
                                    <td class="px-5 py-4 whitespace-nowrap">

                                        <div class="flex items-center gap-2">

                                            <div class="h-7 w-7 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                                {{ strtoupper(substr($incident->user->name ?? 'A', 0, 1)) }}
                                            </div>

                                            <span class="text-sm text-gray-600">
                                                {{ $incident->user->name ?? 'Anonyme' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- DATE --}}
                                    <td class="px-5 py-4 whitespace-nowrap">

                                        <span class="text-xs text-gray-500">
                                            {{ $incident->created_at?->format('d/m/Y') ?? '—' }}
                                        </span>

                                        <div class="text-[10px] text-gray-400">
                                            {{ $incident->created_at?->format('H:i') ?? '' }}
                                        </div>

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="px-5 py-4 whitespace-nowrap text-right">

                                        <div class="flex justify-end items-center gap-3">

                                            {{-- Voir --}}
                                            <a
                                                href="{{ route('incidents.show', $incident) }}"
                                                class="text-green-600 hover:text-green-800 text-xs font-bold transition"
                                            >
                                                Voir
                                            </a>


                                            {{-- Modifier --}}
                                            @can('update', $incident)

                                                <a
                                                    href="{{ route('incidents.edit', $incident) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-bold transition"
                                                >
                                                    Modifier
                                                </a>

                                            @endcan


                                            {{-- Supprimer --}}
                                            @can('delete', $incident)

                                                <form
                                                    action="{{ route('incidents.destroy', $incident) }}"
                                                    method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600 hover:text-red-800 text-xs font-bold transition"
                                                    >
                                                        Supprimer
                                                    </button>

                                                </form>

                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-6 py-16 text-center"
                                    >

                                        <div class="flex flex-col items-center">

                                            <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl mb-4">
                                                📋
                                            </div>

                                            <h3 class="text-base font-semibold text-gray-700">
                                                Aucun incident trouvé
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Aucun signalement ne correspond aux critères sélectionnés.
                                            </p>

                                            @can('create', App\Models\Incident::class)

                                                <a
                                                    href="{{ route('incidents.create') }}"
                                                    class="mt-4 text-sm font-semibold text-green-600 hover:text-green-800"
                                                >
                                                    + Signaler votre premier incident
                                                </a>

                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- ================================================= --}}
                {{-- PAGINATION --}}
                {{-- ================================================= --}}

                @if($incidents->hasPages())

                    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">

                        {{ $incidents->withQueryString()->links() }}

                    </div>

                @endif

            </div>


            {{-- ================================================= --}}
            {{-- FOOTER INFO --}}
            {{-- ================================================= --}}

            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 mt-4 text-xs text-gray-400">

                <p>
                    GeoEcoReport • Gestion des incidents écologiques
                </p>

                <p>
                    Données mises à jour automatiquement
                </p>

            </div>

        </div>

    </div>

</x-app-layout>