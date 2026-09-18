<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inscription - GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        geo: {
                            green: '#009B4D',
                            dark: '#0F172A',
                            blue: '#1D4ED8'
                        }
                    }
                }
            }
        }
    </script>
</head>


<body class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-6">

    {{-- Background decoration --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">

        <div class="absolute -top-20 -left-20 w-72 h-72 bg-green-200/40 rounded-full blur-3xl"></div>

        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-blue-100/50 rounded-full blur-3xl"></div>

    </div>


    {{-- Main container --}}
    <div class="relative w-full max-w-md">


        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">


            {{-- Logo --}}
            <div class="px-6 pt-7 pb-4 text-center">

                <a
                    href="{{ url('/') }}"
                    class="inline-flex flex-col items-center"
                >

                    <img
                        src="{{ asset('images/geeco/logo-mark.png') }}"
                        alt="GeoEcoReport"
                        class="w-16 h-16 object-contain mb-2"
                    >

                    <h1 class="text-2xl font-bold text-slate-900">
                        GeoEcoReport
                    </h1>

                    <p class="mt-1 text-[9px] font-semibold tracking-[0.18em] uppercase text-slate-500">
                        VILLE PROPRE • ENVIRONNEMENT PROTÉGÉ
                    </p>

                </a>

            </div>


            {{-- Form content --}}
            <div class="px-6 pb-6">


                {{-- Title --}}
                <div class="mb-5">

                    <h2 class="text-lg font-bold text-slate-900">
                        Créer un compte
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Rejoignez GeoEcoReport pour signaler et suivre les incidents.
                    </p>

                </div>


                {{-- Errors --}}
                @if ($errors->any())

                    <div class="mb-5 rounded-lg bg-red-50 border border-red-100 px-4 py-3">

                        <div class="flex items-start gap-2">

                            <svg
                                class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 5a1 1 0 012 0v4a1 1 0 11-2 0V5zm1 8a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd"
                                />
                            </svg>

                            <div>

                                <p class="text-xs font-semibold text-red-700">
                                    Vérifiez les informations saisies.
                                </p>

                                <ul class="mt-1 space-y-0.5">

                                    @foreach ($errors->all() as $error)

                                        <li class="text-[11px] text-red-600">
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- Register form --}}
                <form
                    method="POST"
                    action="{{ route('register.submit') }}"
                    class="space-y-4"
                >

                    @csrf


                    {{-- Name --}}
                    <div>

                        <label
                            for="name"
                            class="block text-[11px] font-medium text-slate-700 mb-1.5"
                        >
                            Nom complet
                        </label>

                        <div class="relative">

                            {{-- User icon --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        cx="12"
                                        cy="8"
                                        r="3"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        d="M5 20a7 7 0 0114 0"
                                    />

                                </svg>

                            </div>


                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Votre nom complet"
                                class="w-full h-11 pl-10 pr-4 rounded-lg border-0 bg-slate-50 text-sm text-slate-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition"
                            >

                        </div>


                        @error('name')

                            <p class="text-xs text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label
                            for="email"
                            class="block text-[11px] font-medium text-slate-700 mb-1.5"
                        >
                            Adresse Email
                        </label>

                        <div class="relative">

                            {{-- Email icon --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 8l9 6 9-6"
                                    />

                                    <rect
                                        x="3"
                                        y="5"
                                        width="18"
                                        height="14"
                                        rx="2"
                                    />

                                </svg>

                            </div>


                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="nom@exemple.com"
                                class="w-full h-11 pl-10 pr-4 rounded-lg border-0 bg-slate-50 text-sm text-slate-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition"
                            >

                        </div>


                        @error('email')

                            <p class="text-xs text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Password --}}
                    <div>

                        <label
                            for="password"
                            class="block text-[11px] font-medium text-slate-700 mb-1.5"
                        >
                            Mot de passe
                        </label>

                        <div class="relative">


                            {{-- Lock icon --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <rect
                                        x="5"
                                        y="10"
                                        width="14"
                                        height="10"
                                        rx="2"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        d="M8 10V7a4 4 0 018 0v3"
                                    />

                                </svg>

                            </div>


                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full h-11 pl-10 pr-11 rounded-lg border-0 bg-slate-50 text-sm text-slate-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition"
                            >


                            {{-- Show password --}}
                            <button
                                type="button"
                                onclick="togglePassword('password', 'eyePassword')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                            >

                                <svg
                                    id="eyePassword"
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                    />

                                </svg>

                            </button>

                        </div>


                        <p class="text-[10px] text-gray-400 mt-1">
                            Minimum 8 caractères.
                        </p>


                        @error('password')

                            <p class="text-xs text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Confirm password --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="block text-[11px] font-medium text-slate-700 mb-1.5"
                        >
                            Confirmer le mot de passe
                        </label>

                        <div class="relative">


                            {{-- Lock icon --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <rect
                                        x="5"
                                        y="10"
                                        width="14"
                                        height="10"
                                        rx="2"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        d="M8 10V7a4 4 0 018 0v3"
                                    />

                                </svg>

                            </div>


                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full h-11 pl-10 pr-11 rounded-lg border-0 bg-slate-50 text-sm text-slate-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-green-500 outline-none transition"
                            >


                            {{-- Show confirmation password --}}
                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation', 'eyeConfirmation')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                            >

                                <svg
                                    id="eyeConfirmation"
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5"
                                    />

                                </svg>

                            </button>

                        </div>


                        @error('password_confirmation')

                            <p class="text-xs text-red-600 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full h-11 mt-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-200 flex items-center justify-center gap-2"
                    >

                        <span>
                            Créer mon compte
                        </span>

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12h14M13 6l6 6-6 6"
                            />

                        </svg>

                    </button>

                </form>

            </div>


            {{-- Login link --}}
            <div class="border-t border-gray-100 px-6 py-4 text-center bg-white">

                <p class="text-[11px] text-gray-500">

                    Vous avez déjà un compte ?

                    <a
                        href="{{ route('login') }}"
                        class="font-semibold text-green-700 hover:text-green-800"
                    >
                        Se connecter
                    </a>

                </p>

            </div>

        </div>


        {{-- Footer --}}
        <div class="text-center mt-4">

            <p class="text-[9px] text-gray-500">
                © {{ date('Y') }} GeoEcoReport • Plateforme de supervision territoriale
            </p>

        </div>

    </div>


    {{-- Password JavaScript --}}
    <script>

        function togglePassword(inputId, iconId) {

            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {

                input.type = 'text';

                icon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 3l18 18"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10.5 10.5a2.5 2.5 0 003 3"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.9 5.2A10.7 10.7 0 0112 5c6 0 9.5 7 9.5 7a17.7 17.7 0 01-3.2 4.2"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.6 6.6C3.7 8.4 2.5 12 2.5 12s3.5 7 9.5 7c1.8 0 3.4-.5 4.8-1.2"
                    />
                `;

            } else {

                input.type = 'password';

                icon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                    />
                `;

            }

        }

    </script>

</body>

</html>