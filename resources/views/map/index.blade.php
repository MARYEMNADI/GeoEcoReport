<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Carte des incidents
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Tous les incidents
            </h3>

            <div
                id="all-incidents-map"
                class="w-full h-[600px] rounded-xl border border-gray-300"
            ></div>

        </div>

    </div>

    @push('styles')

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    @endpush


    @push('scripts')

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        const map = L.map('all-incidents-map')
            .setView([32.5353, -6.5342], 12);

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);


        const incidents = @json($incidents);


        incidents.forEach(function (incident) {

            if (!incident.latitude || !incident.longitude) {
                return;
            }

            const marker = L.marker([
                parseFloat(incident.latitude),
                parseFloat(incident.longitude)
            ]).addTo(map);


            marker.bindPopup(`
                <div style="min-width: 180px">

                    <strong>
                        ${incident.title}
                    </strong>

                    <br>

                    <span>
                        ${incident.status}
                    </span>

                    <br><br>

                    <a
                        href="/incidents/${incident.id}"
                        style="color: #2563eb"
                    >
                        Voir l'incident
                    </a>

                </div>
            `);

        });

    });

    </script>

    @endpush

</x-app-layout>