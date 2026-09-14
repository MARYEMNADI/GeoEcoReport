<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Carte des incidents
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            {{-- =========================
                 FILTRES
            ========================== --}}

            <form
                method="GET"
                action="{{ route('map.index') }}"
                class="mb-6"
            >

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Recherche --}}
                    <div>
                        <label
                            for="search"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Recherche
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Titre ou description..."
                            class="w-full rounded-lg border-gray-300"
                        >
                    </div>


                    {{-- Catégorie --}}
                    <div>
                        <label
                            for="category_id"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Catégorie
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full rounded-lg border-gray-300"
                        >
                            <option value="">
                                Toutes les catégories
                            </option>

                            @foreach ($categories as $category)
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
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Statut
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-lg border-gray-300"
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
                        <label
                            for="priority"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Priorité
                        </label>

                        <select
                            id="priority"
                            name="priority"
                            class="w-full rounded-lg border-gray-300"
                        >
                            <option value="">
                                Toutes les priorités
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

                </div>


                <div class="flex gap-3 mt-4">

                    <button
                        type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Filtrer
                    </button>

                    <a
                        href="{{ route('map.index') }}"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                    >
                        Réinitialiser
                    </a>

                </div>

            </form>


            {{-- =========================
                 TITRE
            ========================== --}}

            <div class="flex justify-between items-center mb-4">

                <h3 class="text-lg font-semibold text-gray-800">
                    Tous les incidents
                </h3>

                <span
                    id="incidents-counter"
                    class="text-sm text-gray-500"
                >
                    {{ $incidents->count() }} incident(s)
                </span>

            </div>


            {{-- =========================
                 MAP
            ========================== --}}

            <div
                id="all-incidents-map"
                style="height: 600px; width: 100%;"
                class="rounded-xl border border-gray-300"
            ></div>

        </div>

    </div>


    {{-- =========================
         LEAFLET CSS
    ========================== --}}

    @push('styles')

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />

    @endpush


    {{-- =========================
         LEAFLET JS
    ========================== --}}

    @push('scripts')

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        ></script>

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                console.log('GeoEcoReport Map : démarrage');


                // ==========================
                // INITIALISER LA MAP
                // ==========================

                const mapElement =
                    document.getElementById('all-incidents-map');


                if (!mapElement) {

                    console.error('Map element introuvable');

                    return;
                }


                if (typeof L === 'undefined') {

                    console.error('Leaflet n’est pas chargé');

                    return;
                }


                const map = L.map(mapElement)
                    .setView([32.5353, -6.5342], 12);


                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                // Important pour afficher correctement la map
                setTimeout(function () {
                    map.invalidateSize();
                }, 300);


                // ==========================
                // INCIDENTS
                // ==========================

                const incidents = @json($incidents);

                const markers = [];


                incidents.forEach(function (incident) {

                    if (
                        incident.latitude === null ||
                        incident.latitude === undefined ||
                        incident.longitude === null ||
                        incident.longitude === undefined
                    ) {
                        return;
                    }


                    const latitude =
                        parseFloat(incident.latitude);

                    const longitude =
                        parseFloat(incident.longitude);


                    if (
                        Number.isNaN(latitude) ||
                        Number.isNaN(longitude)
                    ) {
                        return;
                    }


                    const marker = L.marker([
                        latitude,
                        longitude
                    ]);


                    marker.bindPopup(`
                        <div style="min-width:220px">

                            <strong>
                                ${escapeHtml(
                                    incident.title || 'Incident'
                                )}
                            </strong>

                            <br><br>

                            <strong>Catégorie :</strong>
                            ${escapeHtml(
                                incident.category?.name || 'Non définie'
                            )}

                            <br>

                            <strong>Statut :</strong>
                            ${escapeHtml(
                                incident.status || ''
                            )}

                            <br>

                            <strong>Priorité :</strong>
                            ${escapeHtml(
                                incident.priority || 'Non définie'
                            )}

                            <br><br>

                            <a
                                href="/incidents/${incident.id}"
                                style="color:#2563eb;font-weight:600"
                            >
                                Voir l'incident →
                            </a>

                        </div>
                    `);


                    markers.push({
                        marker: marker,
                        incident: incident
                    });


                    marker.addTo(map);

                });


                // ==========================
                // FILTRES
                // ==========================

                const searchInput =
                    document.getElementById('search');

                const categoryInput =
                    document.getElementById('category_id');

                const statusInput =
                    document.getElementById('status');

                const priorityInput =
                    document.getElementById('priority');

                const counter =
                    document.getElementById('incidents-counter');


                function updateMarkers() {

                    const search =
                        (searchInput?.value || '')
                        .toLowerCase()
                        .trim();

                    const category =
                        categoryInput?.value || '';

                    const status =
                        statusInput?.value || '';

                    const priority =
                        priorityInput?.value || '';

                    let visibleCount = 0;


                    markers.forEach(function (item) {

                        const incident =
                            item.incident;


                        const title =
                            (incident.title || '')
                            .toLowerCase();

                        const description =
                            (incident.description || '')
                            .toLowerCase();


                        const matchesSearch =
                            !search ||
                            title.includes(search) ||
                            description.includes(search);


                        const matchesCategory =
                            !category ||
                            String(incident.category_id) ===
                            String(category);


                        const matchesStatus =
                            !status ||
                            incident.status === status;


                        const matchesPriority =
                            !priority ||
                            incident.priority === priority;


                        const visible =
                            matchesSearch &&
                            matchesCategory &&
                            matchesStatus &&
                            matchesPriority;


                        if (visible) {

                            visibleCount++;

                            if (!map.hasLayer(item.marker)) {
                                item.marker.addTo(map);
                            }

                        } else {

                            if (map.hasLayer(item.marker)) {
                                map.removeLayer(item.marker);
                            }

                        }

                    });


                    if (counter) {

                        counter.textContent =
                            visibleCount + ' incident(s)';

                    }

                }


                // Recherche temps réel
                searchInput?.addEventListener(
                    'input',
                    updateMarkers
                );


                categoryInput?.addEventListener(
                    'change',
                    updateMarkers
                );


                statusInput?.addEventListener(
                    'change',
                    updateMarkers
                );


                priorityInput?.addEventListener(
                    'change',
                    updateMarkers
                );


                // ==========================
                // PROTECTION HTML
                // ==========================

                function escapeHtml(value) {

                    const div =
                        document.createElement('div');

                    div.textContent =
                        value ?? '';

                    return div.innerHTML;
                }


                console.log(
                    'GeoEcoReport Map :',
                    markers.length,
                    'markers chargés'
                );

            });

        </script>

    @endpush

</x-app-layout>