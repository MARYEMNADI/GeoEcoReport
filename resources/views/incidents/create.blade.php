<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un Incident</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Signaler un incident écologique</h1>
            <a href="{{ route('incidents.index') }}" class="text-sm text-gray-600 hover:underline">
                &larr; Retour
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('incidents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Titre de l'incident *
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    required 
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >
            </div>

            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Catégorie *
                </label>
                <select 
                    id="category_id" 
                    name="category_id" 
                    required 
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >
                    <option value="">Sélectionnez une catégorie</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description détaillée *
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    required 
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">
                        Latitude *
                    </label>
                    <input 
                        type="text" 
                        id="latitude" 
                        name="latitude" 
                        value="{{ old('latitude', '32.3373') }}" 
                        required 
                        class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                    >
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">
                        Longitude *
                    </label>
                    <input 
                        type="text" 
                        id="longitude" 
                        name="longitude" 
                        value="{{ old('longitude', '-6.3498') }}" 
                        required 
                        class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                    >
                </div>
            </div>

            <div class="mb-4">
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Priorité
                </label>
                <select 
                    id="priority" 
                    name="priority" 
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >
                    <option value="">Sélectionnez une priorité</option>
                    <option value="Faible" {{ old('priority') === 'Faible' ? 'selected' : '' }}>Faible</option>
                    <option value="Moyenne" {{ old('priority', 'Moyenne') === 'Moyenne' ? 'selected' : '' }}>Moyenne</option>
                    <option value="Élevée" {{ old('priority') === 'Élevée' ? 'selected' : '' }}>Élevée</option>
                    <option value="Urgente" {{ old('priority') === 'Urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                    Photo de l'incident (Optionnel)
                </label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                >
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                Soumettre le signalement
            </button>
        </form>

    </div>

</body>
</html>