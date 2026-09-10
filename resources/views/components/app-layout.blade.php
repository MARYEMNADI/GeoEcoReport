<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GeoEcoReport</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Leaflet CSS --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >
</head>

<body class="bg-gray-100 min-h-screen">

    {{-- =========================
         Navigation
    ========================== --}}

    <nav class="bg-white border-b shadow-sm">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}

                <a
                    href="{{ url('/') }}"
                    class="text-xl font-bold text-green-600"
                >
                    GeoEcoReport
                </a>


                {{-- Right side --}}

                @auth

                    <div class="flex items-center gap-4">

                        {{-- =========================
                             Assistant
                        ========================== --}}

                        <a
                            href="{{ route('assistant.index') }}"
                            class="text-sm text-gray-600 hover:text-green-600 font-medium transition"
                        >
                            Assistant
                        </a>


                        {{-- =========================
                             Notifications
                        ========================== --}}

                        <div class="relative">

                            <button
                                type="button"
                                onclick="toggleNotifications()"
                                class="relative p-2 text-gray-600 hover:text-green-600 focus:outline-none"
                            >

                                <span class="text-xl">
                                    🔔
                                </span>


                                {{-- Notification count --}}

                                @if(auth()->user()->unreadNotifications->count() > 0)

                                    <span
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center"
                                    >
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>

                                @endif

                            </button>


                            {{-- =========================
                                 Notifications Dropdown
                            ========================== --}}

                            <div
                                id="notificationsDropdown"
                                class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50"
                            >

                                {{-- Header --}}

                                <div class="p-3 border-b flex justify-between items-center">

                                    <h3 class="font-semibold text-gray-800 text-sm">
                                        Notifications
                                    </h3>


                                    @if(auth()->user()->unreadNotifications->count() > 0)

                                        <form
                                            method="POST"
                                            action="{{ route('notifications.read-all') }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="text-xs text-green-600 hover:underline"
                                            >
                                                Tout marquer comme lu
                                            </button>

                                        </form>

                                    @endif

                                </div>


                                {{-- Notifications list --}}

                                <div class="max-h-96 overflow-y-auto">

                                    @forelse(
                                        auth()->user()->notifications()->take(10)->get()
                                        as $notification
                                    )

                                        <a
                                            href="{{ route(
                                                'incidents.show',
                                                $notification->data['incident_id'] ?? 0
                                            ) }}"
                                            class="block px-4 py-3 border-b hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-green-50' }}"
                                        >

                                            <div class="flex gap-3">

                                                {{-- Icon --}}

                                                <div class="text-lg">

                                                    @if(
                                                        ($notification->data['type'] ?? '')
                                                        === 'incident_assigned'
                                                    )

                                                        👷

                                                    @elseif(
                                                        ($notification->data['type'] ?? '')
                                                        === 'incident_status_changed'
                                                    )

                                                        🔄

                                                    @else

                                                        💬

                                                    @endif

                                                </div>


                                                {{-- Content --}}

                                                <div class="flex-1 min-w-0">

                                                    <p
                                                        class="font-medium text-gray-800 text-sm truncate"
                                                    >
                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                    </p>


                                                    <p class="text-xs text-gray-600 mt-0.5">
                                                        {{ $notification->data['message'] ?? '' }}
                                                    </p>


                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>

                                                </div>

                                            </div>

                                        </a>

                                    @empty

                                        <div class="p-6 text-center text-gray-500 text-sm">

                                            Aucune notification.

                                        </div>

                                    @endforelse

                                </div>

                            </div>

                        </div>


                        {{-- =========================
                             User name
                        ========================== --}}

                        <span class="text-sm text-gray-600">

                            {{ auth()->user()->name }}

                        </span>


                        {{-- =========================
                             Logout
                        ========================== --}}

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="text-sm text-red-600 hover:text-red-800 font-medium"
                            >
                                Déconnexion
                            </button>

                        </form>

                    </div>

                @endauth

            </div>

        </div>

    </nav>


    {{-- =========================
         Header
    ========================== --}}

    @isset($header)

        <header class="bg-white shadow-sm">

            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

                {{ $header }}

            </div>

        </header>

    @endisset


    {{-- =========================
         Success message
    ========================== --}}

    @if(session('success'))

        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">

            <div
                class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg"
            >
                {{ session('success') }}
            </div>

        </div>

    @endif


    {{-- =========================
         Error message
    ========================== --}}

    @if(session('error'))

        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">

            <div
                class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg"
            >
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================
         Main content
    ========================== --}}

    <main>

        {{ $slot }}

    </main>


    {{-- =========================
         Leaflet JavaScript
    ========================== --}}

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    {{-- =========================
         Notifications JavaScript
    ========================== --}}

    <script>

        function toggleNotifications() {

            const dropdown =
                document.getElementById('notificationsDropdown');

            if (dropdown) {

                dropdown.classList.toggle('hidden');

            }

        }


        document.addEventListener('click', function (event) {

            const dropdown =
                document.getElementById('notificationsDropdown');

            const button =
                event.target.closest('button');


            if (
                dropdown &&
                !dropdown.contains(event.target) &&
                !(button && button.closest('.relative'))
            ) {

                dropdown.classList.add('hidden');

            }
<a href="{{ route('map.index') }}" class="text-sm text-gray-600 hover:text-green-600">
    Carte
</a>
        });

    </script>


</body>

</html>