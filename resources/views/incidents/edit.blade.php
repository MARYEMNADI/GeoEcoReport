<x-app-layout>

{{-- Header --}}
<x-slot name="header">

    <div class="flex items-center justify-between">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier l'incident
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $incident->title }}
            </p>

        </div>

        <a
            href="{{ route('incidents.show', $incident) }}"
            class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-200 transition"
        >
            ← Retour
        </a>

    </div>

</x-slot>


{{-- Contenu --}}
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Erreurs générales --}}
    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">

            <div class="font-semibold mb-2">
                Veuillez corriger les erreurs suivantes :
            </div>

            <ul class="list-disc pl-5 space-y-1 text-sm">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Formulaire --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Titre --}}
        <div class="px-6 py-5 border-b border-gray-200">

            <h1 class="text-2xl font-bold text-gray-800">
                Modifier l'incident
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Modifiez les informations de votre signalement.
            </p>

        </div>


        <form
            action="{{ route('incidents.update', $incident) }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-6 space-y-6"
        >

            @csrf

            @method('PUT')


            {{-- =========================
                 Titre
            ========================== --}}

            <div>

                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Titre
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $incident->title) }}"
                    required
                    maxlength="255"
                    placeholder="Ex : Nid-de-poule important"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                @error('title')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =========================
                 Description
            ========================== --}}

            <div>

                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                    required
                    placeholder="Décrivez l'incident..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >{{ old('description', $incident->description) }}</textarea>

                @error('description')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =========================
                 Catégorie
            ========================== --}}

            <div>

                <label
                    for="category_id"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Catégorie
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                    <option value="">
                        Choisir une catégorie
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(
                                old('category_id', $incident->category_id) == $category->id
                            )
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

                @error('category_id')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =========================
                 Localisation
            ========================== --}}

            <div class="border-t border-gray-200 pt-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Localisation
                </h3>


                {{-- Latitude --}}
                <div class="mb-4">

                    <label
                        for="latitude"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Latitude
                    </label>

                    <input
                        type="number"
                        step="any"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude', $incident->latitude) }}"
                        required
                        placeholder="Ex : 32.53530000"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('latitude')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Longitude --}}
                <div>

                    <label
                        for="longitude"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Longitude
                    </label>

                    <input
                        type="number"
                        step="any"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude', $incident->longitude) }}"
                        required
                        placeholder="Ex : -6.53420000"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >

                    @error('longitude')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- =========================
                 Image
            ========================== --}}

            <div class="border-t border-gray-200 pt-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Photo
                </h3>


                {{-- Images actuelles --}}
                @if ($incident->images && $incident->images->count())

                    <div class="mb-5">

                        <p class="text-sm font-medium text-gray-700 mb-3">
                            Photos actuelles
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

                            @foreach ($incident->images as $image)

                                <div class="relative">

                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="Photo de l'incident"
                                        class="w-full h-40 object-cover rounded-lg border border-gray-200"
                                    >

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- Nouvelle image --}}
                <label
                    for="image"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Ajouter une nouvelle photo
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2"
                >

                <p class="text-xs text-gray-500 mt-2">
                    Formats acceptés : JPG, JPEG, PNG, WEBP — 5 Mo maximum.
                </p>

                @error('image')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =========================
                 Informations
            ========================== --}}

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">

                <p class="text-sm text-gray-600">

                    <strong>
                        Statut actuel :
                    </strong>

                    {{ $incident->status }}

                </p>

                <p class="text-sm text-gray-600 mt-1">

                    <strong>
                        Priorité :
                    </strong>

                    {{ $incident->priority ?? 'Moyenne' }}

                </p>

            </div>


            {{-- =========================
                 Buttons
            ========================== --}}

            <div class="flex flex-col sm:flex-row items-center gap-3 pt-6 border-t border-gray-200">

                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition"
                >
                    Enregistrer les modifications
                </button>


                <a
                    href="{{ route('incidents.show', $incident) }}"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-md transition"
                >
                    Annuler
                </a>

            </div>

        </form>

    </div>

</div>


</x-app-layout>
