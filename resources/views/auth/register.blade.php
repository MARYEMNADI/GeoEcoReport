<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inscription - GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">

        {{-- Titre --}}
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Créer un compte
        </h2>


        {{-- Erreurs --}}
        @if ($errors->any())

            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md text-sm">

                <ul class="list-disc pl-5">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Formulaire --}}
        <form
            method="POST"
            action="{{ route('register.submit') }}"
        >

            @csrf


            {{-- Nom --}}
            <div class="mb-4">

                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Nom complet
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >

            </div>


            {{-- Email --}}
            <div class="mb-4">

                <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Adresse Email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >

            </div>


            {{-- Password --}}
            <div class="mb-4">

                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Mot de passe
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >

                <p class="text-xs text-gray-500 mt-1">
                    Minimum 8 caractères.
                </p>

            </div>


            {{-- Confirmation --}}
            <div class="mb-6">

                <label
                    for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Confirmer le mot de passe
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >

            </div>


            {{-- Bouton --}}
            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white
                       font-semibold py-2 px-4 rounded-md transition"
            >
                Créer mon compte
            </button>

        </form>


        {{-- Lien Login --}}
        <div class="mt-6 text-center">

            <p class="text-sm text-gray-600">

                Vous avez déjà un compte ?

                <a
                    href="{{ route('login') }}"
                    class="text-green-600 hover:text-green-700 font-semibold"
                >
                    Se connecter
                </a>

            </p>

        </div>

    </div>

</body>

</html>