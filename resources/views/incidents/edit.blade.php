<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'incident - GeoEcoReport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen py-10 px-4">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">

        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Modifier l'incident #{{ $incident->id }}
            </h1>

            <a
                href="{{ route('incidents.show', $incident) }}"
                class="text-sm text-gray-600 hover:underline"
            >
                &larr; Retour
            </a>
        </div>

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <form
            action="{{ route('incidents.update', $incident) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <!-- Titre -->
            <div class="mb-4">
                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Titre de l'incident *
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $incident->title) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <!-- Catégorie -->
            <div class="mb-4">
                <label
                    for="category_id"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Catégorie *
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">
                        Sélectionnez une catégorie
                    </option>

                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ old('category_id', $incident->category_id) == $category->id ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Description détaillée *
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >{{ old('description', $incident->description) }}</textarea>
            </div>

            <!-- Coordonnées -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label
                        for="latitude"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Latitude
                    </label>

                    <input
                        type="text"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude', $incident->latitude) }}"
                        placeholder="32.3373"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label
                        for="longitude"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Longitude
                    </label>

                    <input
                        type="text"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude', $incident->longitude) }}"
                        placeholder="-6.3498"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

            </div>

            <!-- Priorité -->
            <div class="mb-4">
                <label
                    for="priority"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Priorité
                </label>

                <select
                    id="priority"
                    name="priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                    <option
                        value="Faible"
                        {{ old('priority', $incident->priority) === 'Faible' ? 'selected' : '' }}
                    >
                        Faible
                    </option>

                    <option
                        value="Moyenne"
                        {{ old('priority', $incident->priority) === 'Moyenne' ? 'selected' : '' }}
                    >
                        Moyenne
                    </option>

                    <option
                        value="Élevée"
                        {{ old('priority', $incident->priority) === 'Élevée' ? 'selected' : '' }}
                    >
                        Élevée
                    </option>

                    <option
                        value="Urgente"
                        {{ old('priority', $incident->priority) === 'Urgente' ? 'selected' : '' }}
                    >
                        Urgente
                    </option>
                </select>
            </div>

            <!-- Photo de l'incident -->
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                    Photo de l'incident (Optionnel)
                </label>

                @if($incident->images->isNotEmpty())
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Photo actuelle :</p>
                        <img 
                            src="{{ asset('storage/' . $incident->images->first()->image_path) }}" 
                            alt="Photo actuelle" 
                            class="w-32 h-32 object-cover rounded-md border"
                        >
                    </div>
                @endif

                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                >
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">

                <a
                    href="{{ route('incidents.show', $incident) }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Enregistrer les modifications
                </button>

            </div>

        </form>

    </div>

</body>
</html>