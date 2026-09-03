<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liste des Incidents - GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-10 px-6">

    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Liste des Incidents Écologiques
                </h1>

                <p class="text-sm text-gray-600 mt-1">
                    Gestion et suivi des signalements écologiques
                </p>
            </div>

            <!-- Bouton création -->
            @can('create', App\Models\Incident::class)
                <a
                    href="{{ route('incidents.create') }}"
                    class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200"
                >
                    + Signaler un incident
                </a>
            @endcan

        </div>

        <!-- Message de succès -->
        @if (session('success'))
            <div
                class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md"
                role="alert"
            >
                {{ session('success') }}
            </div>
        @endif

        <!-- Message d'erreur -->
        @if (session('error'))
            <div
                class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md"
                role="alert"
            >
                {{ session('error') }}
            </div>
        @endif

        <!-- Tableau -->
        <div class="bg-white rounded-lg shadow overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <!-- Header tableau -->
                    <thead class="bg-gray-50">
                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Photo
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Titre & Catégorie
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Priorité
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Statut
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Signaleur
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Date
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <!-- Corps du tableau -->
                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($incidents as $incident)

                            <tr class="hover:bg-gray-50 transition">

                                <!-- Photo -->
                                <td class="px-6 py-4 whitespace-nowrap">

                                    @if($incident->images->isNotEmpty())

                                        <img
                                            src="{{ asset('storage/' . $incident->images->first()->image_path) }}"
                                            alt="Photo de {{ $incident->title }}"
                                            class="h-12 w-12 object-cover rounded-md border"
                                        >

                                    @else

                                        <div class="h-12 w-12 rounded-md bg-gray-100 border flex items-center justify-center text-xs text-gray-400">
                                            Pas d'img
                                        </div>

                                    @endif

                                </td>

                                <!-- Titre + catégorie -->
                                <td class="px-6 py-4">

                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $incident->title }}
                                    </div>

                                    <div class="text-xs text-green-600 font-medium mt-1">
                                        {{ $incident->category->name ?? 'Non spécifiée' }}
                                    </div>

                                </td>

                                <!-- Priorité -->
                                <td class="px-6 py-4 whitespace-nowrap">

                                    @php
                                        $priorityClasses = match($incident->priority) {
                                            'Urgente' => 'bg-red-100 text-red-800',
                                            'Élevée' => 'bg-orange-100 text-orange-800',
                                            'Moyenne' => 'bg-yellow-100 text-yellow-800',
                                            'Faible' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp

                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClasses }}"
                                    >
                                        {{ $incident->priority ?? 'Moyenne' }}
                                    </span>

                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4 whitespace-nowrap">

                                    @php
                                        $statusClasses = match($incident->status) {
                                            'En attente' => 'bg-yellow-100 text-yellow-800',
                                            'En cours de traitement' => 'bg-blue-100 text-blue-800',
                                            'Résolu' => 'bg-green-100 text-green-800',
                                            'Rejeté' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp

                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}"
                                    >
                                        {{ $incident->status }}
                                    </span>

                                </td>

                                <!-- Signaleur -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $incident->user->name ?? 'Anonyme' }}
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $incident->created_at->format('d/m/Y H:i') }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                    <div class="flex justify-end items-center gap-3">

                                        <!-- Voir détails -->
                                        <a
                                            href="{{ route('incidents.show', $incident->id) }}"
                                            class="text-green-600 hover:text-green-900 font-bold"
                                        >
                                            Voir détails
                                        </a>

                                        <!-- Modifier -->
                                        @can('update', $incident)

                                            <a
                                                href="{{ route('incidents.edit', $incident->id) }}"
                                                class="text-blue-600 hover:text-blue-900 font-bold"
                                            >
                                                Modifier
                                            </a>

                                        @endcan

                                        <!-- Supprimer -->
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
                                                    class="text-red-600 hover:text-red-900 font-bold"
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
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Aucun incident signalé pour le moment.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            @if($incidents->hasPages())

                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $incidents->links() }}
                </div>

            @endif

        </div>

    </div>

</body>
</html>