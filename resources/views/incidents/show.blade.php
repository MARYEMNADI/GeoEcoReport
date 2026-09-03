<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $incident->title }} - GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-10 px-6">

<div class="max-w-5xl mx-auto">

    {{-- =========================
         Messages
    ========================== --}}

    @if (session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =========================
         Header
    ========================== --}}

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">

            <div>

                <p class="text-sm text-gray-500 mb-2">
                    Incident #{{ $incident->id }}
                </p>

                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $incident->title }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Signalé le {{ $incident->created_at->format('d/m/Y à H:i') }}
                </p>

            </div>


            {{-- Actions --}}

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('incidents.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-md"
                >
                    ← Retour
                </a>


                @can('update', $incident)

                    <a
                        href="{{ route('incidents.edit', $incident) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md"
                    >
                        Modifier
                    </a>

                @endcan


                @can('delete', $incident)

                    <form
                        action="{{ route('incidents.destroy', $incident) }}"
                        method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md"
                        >
                            Supprimer
                        </button>

                    </form>

                @endcan

            </div>

        </div>

    </div>


    {{-- =========================
         Statut / Priorité / Catégorie
    ========================== --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">


        {{-- Statut --}}

        <div class="bg-white rounded-lg shadow p-6">

            <p class="text-sm text-gray-500 mb-2">
                Statut
            </p>

            @php
                $statusClasses = match($incident->status) {

                    'En attente'
                        => 'bg-yellow-100 text-yellow-800',

                    'En cours de traitement'
                        => 'bg-blue-100 text-blue-800',

                    'Résolu'
                        => 'bg-green-100 text-green-800',

                    'Rejeté'
                        => 'bg-red-100 text-red-800',

                    default
                        => 'bg-gray-100 text-gray-800',
                };
            @endphp

            <span
                class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses }}"
            >
                {{ $incident->status }}
            </span>


            {{-- Changement de statut --}}
            {{-- Technicien + Administrateur uniquement --}}

            @can('changeStatus', $incident)

                <form
                    action="{{ route('incidents.status.update', $incident) }}"
                    method="POST"
                    class="mt-4"
                >

                    @csrf
                    @method('PATCH')

                    <label
                        for="status"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Modifier le statut
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full border-gray-300 rounded-md shadow-sm"
                    >

                        <option
                            value="En attente"
                            {{ $incident->status === 'En attente' ? 'selected' : '' }}
                        >
                            En attente
                        </option>

                        <option
                            value="En cours de traitement"
                            {{ $incident->status === 'En cours de traitement' ? 'selected' : '' }}
                        >
                            En cours de traitement
                        </option>

                        <option
                            value="Résolu"
                            {{ $incident->status === 'Résolu' ? 'selected' : '' }}
                        >
                            Résolu
                        </option>

                        <option
                            value="Rejeté"
                            {{ $incident->status === 'Rejeté' ? 'selected' : '' }}
                        >
                            Rejeté
                        </option>

                    </select>

                    <button
                        type="submit"
                        class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                    >
                        Mettre à jour
                    </button>

                </form>

            @endcan

        </div>


        {{-- Priorité --}}

        <div class="bg-white rounded-lg shadow p-6">

            <p class="text-sm text-gray-500 mb-2">
                Priorité
            </p>

            @php
                $priorityClasses = match($incident->priority) {

                    'Urgente'
                        => 'bg-red-100 text-red-800',

                    'Élevée'
                        => 'bg-orange-100 text-orange-800',

                    'Moyenne'
                        => 'bg-yellow-100 text-yellow-800',

                    'Faible'
                        => 'bg-blue-100 text-blue-800',

                    default
                        => 'bg-gray-100 text-gray-800',
                };
            @endphp

            <span
                class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $priorityClasses }}"
            >
                {{ $incident->priority ?? 'Moyenne' }}
            </span>

        </div>


        {{-- Catégorie --}}

        <div class="bg-white rounded-lg shadow p-6">

            <p class="text-sm text-gray-500 mb-2">
                Catégorie
            </p>

            <p class="text-lg font-semibold text-green-600">
                {{ $incident->category->name ?? 'Non spécifiée' }}
            </p>

            @if ($incident->category)

                <p class="text-xs text-gray-500 mt-1">
                    {{ $incident->category->type }}
                </p>

            @endif

        </div>

    </div>


    {{-- =========================
         Informations
    ========================== --}}

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">

        <h2 class="text-xl font-bold text-gray-800 mb-5">
            Informations sur l'incident
        </h2>


        {{-- Description --}}

        <div class="mb-6">

            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Description
            </h3>

            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $incident->description }}
            </p>

        </div>


        {{-- Signaleur --}}

        <div class="mb-6">

            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Signalé par
            </h3>

            <p class="text-gray-700">
                {{ $incident->user->name ?? 'Anonyme' }}
            </p>

            @if ($incident->user)

                <p class="text-sm text-gray-500">
                    {{ $incident->user->email }}
                </p>

            @endif

        </div>


        {{-- Localisation --}}

        <div>

            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Localisation
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="bg-gray-50 border rounded-md p-4">

                    <p class="text-xs text-gray-500">
                        Latitude
                    </p>

                    <p class="font-semibold text-gray-800">
                        {{ $incident->latitude ?? 'Non renseignée' }}
                    </p>

                </div>


                <div class="bg-gray-50 border rounded-md p-4">

                    <p class="text-xs text-gray-500">
                        Longitude
                    </p>

                    <p class="font-semibold text-gray-800">
                        {{ $incident->longitude ?? 'Non renseignée' }}
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================
         Photos
    ========================== --}}

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">

        <h2 class="text-xl font-bold text-gray-800 mb-5">
            Photos
        </h2>

        @if ($incident->images->isNotEmpty())

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                @foreach ($incident->images as $image)

                    <div class="border rounded-lg overflow-hidden bg-gray-50">

                        <img
                            src="{{ asset('storage/' . $image->image_path) }}"
                            alt="Photo de {{ $incident->title }}"
                            class="w-full h-56 object-cover"
                        >

                    </div>

                @endforeach

            </div>

        @else

            <p class="text-gray-500">
                Aucune photo disponible pour cet incident.
            </p>

        @endif

    </div>


    {{-- =========================
         GeoEco Assistant
    ========================== --}}

    @if ($incident->ai_summary || $incident->ai_suggested_category)

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">

            <h2 class="text-xl font-bold text-gray-800 mb-5">
                GeoEco Assistant
            </h2>


            @if ($incident->ai_summary)

                <div class="mb-4">

                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                        Résumé IA
                    </h3>

                    <p class="text-gray-700 leading-relaxed">
                        {{ $incident->ai_summary }}
                    </p>

                </div>

            @endif


            @if ($incident->ai_suggested_category)

                <div>

                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                        Catégorie suggérée par l'IA
                    </h3>

                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ $incident->ai_suggested_category }}
                    </span>

                </div>

            @endif

        </div>

    @endif


    {{-- =========================
         Commentaires
    ========================== --}}

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">

        <h2 class="text-xl font-bold text-gray-800 mb-5">
            Commentaires
        </h2>


        {{-- Liste --}}

        <div class="space-y-4 mb-6">

            @forelse ($incident->comments as $comment)

                <div class="p-4 bg-gray-50 rounded-md border">

                    <div class="flex justify-between items-center mb-1">

                        <span class="font-semibold text-gray-800">
                            {{ $comment->user->name ?? 'Utilisateur' }}
                        </span>

                        <span class="text-xs text-gray-500">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </span>

                    </div>

                    <p class="text-gray-700 text-sm whitespace-pre-line">
                        {{ $comment->content }}
                    </p>

                </div>

            @empty

                <p class="text-gray-500 text-sm">
                    Aucun commentaire pour le moment.
                </p>

            @endforelse

        </div>


        {{-- Formulaire --}}

        @auth

            <form
                action="{{ route('comments.store', $incident) }}"
                method="POST"
            >

                @csrf

                <div class="mb-3">

                    <label
                        for="content"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Ajouter un commentaire
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="3"
                        maxlength="1000"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                        placeholder="Écrire un commentaire..."
                    >{{ old('content') }}</textarea>

                </div>


                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-md"
                >
                    Publier
                </button>

            </form>

        @endauth

    </div>


    {{-- =========================
         Affectation
    ========================== --}}

    @can('assign', $incident)

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">

            <h2 class="text-xl font-bold text-gray-800 mb-5">
                Affectation du technicien
            </h2>

            <form
                action="{{ route('incidents.assign', $incident) }}"
                method="POST"
            >

                @csrf

                {{-- Technicien --}}

                <div class="mb-4">

                    <label
                        for="technicien_id"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Choisir un technicien
                    </label>

                    <select
                        id="technicien_id"
                        name="technicien_id"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm"
                    >

                        <option value="">
                            Sélectionnez un technicien
                        </option>

                        @forelse ($techniciens as $technicien)

                            <option value="{{ $technicien->id }}">
                                {{ $technicien->name }} — {{ $technicien->email }}
                            </option>

                        @empty

                            <option value="">
                                Aucun technicien disponible
                            </option>

                        @endforelse

                    </select>

                </div>


                {{-- Instructions --}}

                <div class="mb-4">

                    <label
                        for="instructions"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Instructions
                    </label>

                    <textarea
                        id="instructions"
                        name="instructions"
                        rows="4"
                        maxlength="1000"
                        class="w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Instructions pour le technicien..."
                    >{{ old('instructions') }}</textarea>

                </div>


                <button
                    type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md"
                >
                    Affecter le technicien
                </button>

            </form>

        </div>

    @endcan


    {{-- =========================
         Affectations existantes
    ========================== --}}

    @if ($incident->affectations->isNotEmpty())

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">

            <h2 class="text-xl font-bold text-gray-800 mb-5">
                Historique des affectations
            </h2>

            <div class="space-y-4">

                @foreach ($incident->affectations as $affectation)

                    <div class="border rounded-lg p-4 bg-gray-50">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Technicien
                                </p>

                                <p class="font-semibold text-gray-800">
                                    {{ $affectation->technicien->name ?? 'Non affecté' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Date d'affectation
                                </p>

                                <p class="font-semibold text-gray-800">
                                    {{
                                        $affectation->date_affectation?->format('d/m/Y H:i')
                                        ?? 'Non renseignée'
                                    }}
                                </p>

                            </div>

                        </div>


                        @if ($affectation->instructions)

                            <div class="mt-4">

                                <p class="text-xs text-gray-500">
                                    Instructions
                                </p>

                                <p class="text-gray-700 mt-1 whitespace-pre-line">
                                    {{ $affectation->instructions }}
                                </p>

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>

    @endif

</div>

</body>
</html>