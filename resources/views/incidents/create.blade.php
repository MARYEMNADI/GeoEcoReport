<x-app-layout>

    {{-- Header --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Signaler un incident écologique
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Signalez un problème environnemental dans votre région.
                </p>
            </div>

            <a
                href="{{ route('incidents.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900 hover:underline"
            >
                ← Retour à la liste
            </a>
        </div>
    </x-slot>

    {{-- Contenu --}}
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

            {{-- Messages d'erreurs --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <span class="text-red-600 font-semibold">
                            Erreurs de validation
                        </span>
                    </div>

                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire --}}
            <form
                action="{{ route('incidents.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                {{-- ========================= --}}
                {{-- Titre --}}
                {{-- ========================= --}}
                <div class="mb-5">

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
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                        placeholder="Exemple : Nid-de-poule dangereux"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    >

                    @error('title')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ========================= --}}
                {{-- Catégorie --}}
                {{-- ========================= --}}
                <div class="mb-5">

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
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    >

                        <option value="">
                            -- Sélectionner une catégorie --
                        </option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}
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


                {{-- ========================= --}}
                {{-- Description --}}
                {{-- ========================= --}}
                <div class="mb-6">

                    <label
                        for="description"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Description *
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        placeholder="Décrivez l'incident en détail..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ========================= --}}
                {{-- Localisation Leaflet --}}
                {{-- ========================= --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 mb-6">

                    <div class="flex items-center justify-between mb-4">

                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">
                                📍 Localisation
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Choisissez l'emplacement de l'incident sur la carte.
                            </p>
                        </div>

                    </div>


                    {{-- Carte --}}
                    <div
                        id="map"
                        class="w-full h-80 rounded-xl border border-gray-300 overflow-hidden"
                    ></div>


                    {{-- Coordonnées --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                        {{-- Latitude --}}
                        <div>

                            <label
                                for="latitude"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Latitude
                            </label>

                            <input
                                type="text"
                                id="latitude"
                                name="latitude"
                                value="{{ old('latitude', '32.53530000') }}"
                                readonly
                                required
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed"
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
                                class="block text-sm font-medium text-gray-700"
                            >
                                Longitude
                            </label>

                            <input
                                type="text"
                                id="longitude"
                                name="longitude"
                                value="{{ old('longitude', '-6.53420000') }}"
                                readonly
                                required
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed"
                            >

                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- Bouton position --}}
                    <button
                        type="button"
                        id="my-location"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
                    >
                        📍 Ma position
                    </button>


                    <p class="text-sm text-gray-500 mt-2">
                        Cliquez sur la carte pour choisir une position ou déplacez le marqueur.
                    </p>

                </div>


                {{-- ========================= --}}
                {{-- Photo --}}
                {{-- ========================= --}}
                <div class="mb-6">

                    <label
                        for="image"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Photo de l'incident
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                    >

                    <p class="text-xs text-gray-500 mt-2">
                        Formats acceptés : JPG, JPEG, PNG, WEBP.
                    </p>

                    @error('image')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ========================= --}}
                {{-- Actions --}}
                {{-- ========================= --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">

                    <a
                        href="{{ route('incidents.index') }}"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                    >
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                    >
                        Signaler l'incident
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================= --}}
    {{-- Leaflet JavaScript --}}
    {{-- ========================= --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // Inputs
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const locationButton = document.getElementById('my-location');

            // Valeurs actuelles
            const defaultLat =
                parseFloat(latitudeInput.value) || 32.53530000;

            const defaultLng =
                parseFloat(longitudeInput.value) || -6.53420000;


            // =========================
            // Création de la carte
            // =========================

            const map = L.map('map').setView(
                [defaultLat, defaultLng],
                13
            );


            // =========================
            // OpenStreetMap
            // =========================

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            // =========================
            // Marker
            // =========================

            let marker = L.marker(
                [defaultLat, defaultLng],
                {
                    draggable: true
                }
            ).addTo(map);


            marker.bindPopup(
                '<strong>Position de l\'incident</strong>'
            );


            // =========================
            // Mise à jour coordonnées
            // =========================

            function updateCoordinates(lat, lng) {

                latitudeInput.value = lat.toFixed(8);

                longitudeInput.value = lng.toFixed(8);

                marker.setLatLng([lat, lng]);

            }


            // =========================
            // Click sur la carte
            // =========================

            map.on('click', function (event) {

                const lat = event.latlng.lat;

                const lng = event.latlng.lng;

                updateCoordinates(lat, lng);

            });


            // =========================
            // Drag du marker
            // =========================

            marker.on('dragend', function () {

                const position = marker.getLatLng();

                updateCoordinates(
                    position.lat,
                    position.lng
                );

            });


            // =========================
            // Ma position
            // =========================

            locationButton.addEventListener(
                'click',
                function () {

                    if (!navigator.geolocation) {

                        alert(
                            'La géolocalisation n’est pas supportée par votre navigateur.'
                        );

                        return;
                    }


                    // Petit feedback
                    locationButton.disabled = true;

                    locationButton.innerHTML =
                        '⏳ Localisation en cours...';


                    navigator.geolocation.getCurrentPosition(

                        function (position) {

                            const lat =
                                position.coords.latitude;

                            const lng =
                                position.coords.longitude;


                            // Centrer la carte
                            map.setView(
                                [lat, lng],
                                16
                            );


                            // Mettre à jour le marker
                            updateCoordinates(
                                lat,
                                lng
                            );


                            marker
                                .bindPopup(
                                    '<strong>Votre position</strong>'
                                )
                                .openPopup();


                            // Réactiver bouton
                            locationButton.disabled = false;

                            locationButton.innerHTML =
                                '📍 Ma position';

                        },


                        function (error) {

                            let message =
                                'Impossible de récupérer votre position.';


                            if (error.code === 1) {

                                message =
                                    'Vous avez refusé l’accès à votre position.';

                            } else if (error.code === 2) {

                                message =
                                    'Votre position est indisponible.';

                            } else if (error.code === 3) {

                                message =
                                    'La récupération de votre position a expiré.';

                            }


                            alert(message);


                            // Réactiver bouton
                            locationButton.disabled = false;

                            locationButton.innerHTML =
                                '📍 Ma position';

                        },

                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }

                    );

                }
            );

        });

    </script>

</x-app-layout>