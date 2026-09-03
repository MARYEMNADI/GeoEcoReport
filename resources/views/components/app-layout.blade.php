<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    {{-- Navigation --}}
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


                {{-- User --}}
                @auth

                    <div class="flex items-center gap-4">

                        <span class="text-sm text-gray-600">
                            {{ auth()->user()->name }}
                        </span>

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


    {{-- Header --}}
    @isset($header)

        <header class="bg-white shadow-sm">

            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

                {{ $header }}

            </div>

        </header>

    @endisset


    {{-- Main content --}}
    <main>

        {{ $slot }}

    </main>

</body>

</html>