<x-app-layout>
    <x-slot:header>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Carte des incidents
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Visualisation géographique de tous les signalements
                </p>
            </div>
            <a href="{{ route('incidents.index') }}"
               class="text-sm text-gray-600 hover:text-green-600">
                ← Retour à la liste
            </a>
        </div>
    </x-slot:header>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- Filtres simples --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('map.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">Tous</option>
                        @foreach(['En attente', 'En cours de traitement', 'Résolu', 'Rejeté'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catégorie</label>
                    <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                    Filtrer
                </button>

                @if(request()->hasAny(['status', 'category_id']))
                    <a href="{{ route('map.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- Carte --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>

        {{-- Légende --}}
        <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span> En attente
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span> En cours
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span> Résolu
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span> Rejeté
            </div>
            <div class="ml-auto text-gray-500">
                {{ $incidents->count() }} incident(s) affiché(s)
            </div>
        </div>
    </div>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Centre par défaut (Maroc - Settat / Casablanca region)
            const map = L.map('map').setView([32.3373, -6.3498], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // Couleurs selon le statut
            const statusColors = {
                'En attente': '#facc15',
                'En cours de traitement': '#3b82f6',
                'Résolu': '#22c55e',
                'Rejeté': '#ef4444'
            };

            // Données des incidents (envoyées depuis le controller)
            const incidents = @json($incidentsForMap);

            const bounds = [];

            incidents.forEach(function (incident) {
                if (!incident.latitude || !incident.longitude) return;

                const color = statusColors[incident.status] || '#6b7280';

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${color};
                        width: 18px;
                        height: 18px;
                        border-radius: 50%;
                        border: 2px solid white;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
                    "></div>`,
                    iconSize: [18, 18],
                    iconAnchor: [9, 9]
                });

                const marker = L.marker([incident.latitude, incident.longitude], { icon })
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width:180px">
                            <strong>${incident.title}</strong><br>
                            <span style="color:#16a34a;font-size:12px">${incident.category_name || ''}</span><br>
                            <span style="font-size:12px">Statut : ${incident.status}</span><br>
                            <a href="/incidents/${incident.id}" style="color:#16a34a;font-size:13px;font-weight:600">
                                Voir le détail →
                            </a>
                        </div>
                    `);

                bounds.push([incident.latitude, incident.longitude]);
            });

            // Ajuster la vue pour inclure tous les marqueurs
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }

            setTimeout(function () {
                map.invalidateSize();
            }, 300);
        });
    </script>
</x-app-layout>