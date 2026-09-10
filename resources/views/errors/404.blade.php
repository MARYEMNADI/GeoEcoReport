<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page introuvable | GeoEcoReport</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

    <div class="text-center max-w-lg">

        <img
            src="{{ asset('images/geeco/logo-mark.png') }}"
            alt="GeoEcoReport"
            class="w-20 h-20 mx-auto mb-6 object-contain"
        >

        <h1 class="text-8xl font-bold text-green-600">
            404
        </h1>

        <h2 class="text-2xl font-bold text-gray-800 mt-4">
            Page introuvable
        </h2>

        <p class="text-gray-600 mt-3">
            Désolé, la page que vous recherchez n'existe pas
            ou a été déplacée.
        </p>

        <div class="mt-8 flex justify-center gap-3">

            <a
                href="{{ route('dashboard') }}"
                class="px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
            >
                Retour au dashboard
            </a>

            <a
                href="{{ route('incidents.index') }}"
                class="px-5 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
            >
                Voir les incidents
            </a>

        </div>

    </div>

</body>
</html>