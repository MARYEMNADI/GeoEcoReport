<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GeoEcoReport - Signalement intelligent</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        eco: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- =========================
         NAVBAR
    ========================== --}}

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="h-16 flex items-center justify-between">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3">

                    <img
                        src="{{ asset('images/geeco/logo-mark.png') }}"
                        alt="GeoEcoReport"
                        class="w-10 h-10 object-contain"
                    >

                    <div>
                        <div class="font-bold text-xl text-green-700">
                            GeoEcoReport
                        </div>

                        <div class="text-xs text-gray-500 hidden sm:block">
                            Ville propre • Environnement protégé
                        </div>
                    </div>

                </a>


                {{-- Navigation --}}
                <div class="flex items-center gap-3">

                    @auth

                        <a
                            href="{{ route('dashboard') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700"
                        >
                            Dashboard
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700"
                        >
                            Connexion
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition"
                        >
                            Créer un compte
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </nav>


    {{-- =========================
         HERO
    ========================== --}}

    <section class="relative min-h-[620px] flex items-center overflow-hidden">

        {{-- Image --}}
        <img
            src="{{ asset('images/geeco/hero-environment.jpg') }}"
            alt="Environnement urbain"
            class="absolute inset-0 w-full h-full object-cover"
        >

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/55"></div>


        {{-- Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">

            <div class="max-w-3xl text-white">

                <div class="inline-flex items-center gap-2 bg-green-600/90 px-4 py-2 rounded-full text-sm font-medium mb-6">
                    🌿 Plateforme citoyenne intelligente
                </div>


                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">

                    Signalez les incidents.

                    <span class="text-green-400">
                        Améliorons notre ville.
                    </span>

                </h1>


                <p class="text-lg md:text-xl text-gray-100 leading-relaxed mb-8 max-w-2xl">

                    GeoEcoReport permet aux citoyens de signaler facilement
                    les incidents urbains et environnementaux,
                    de les localiser sur une carte et de suivre leur traitement.

                </p>


                <div class="flex flex-col sm:flex-row gap-4">

                    @auth

                        <a
                            href="{{ route('incidents.create') }}"
                            class="inline-flex justify-center items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-lg"
                        >
                            🚨 Signaler un incident
                        </a>

                        <a
                            href="{{ route('map.index') }}"
                            class="inline-flex justify-center items-center px-6 py-3 bg-white/95 hover:bg-white text-gray-800 font-semibold rounded-xl transition"
                        >
                            🗺️ Voir la carte
                        </a>

                    @else

                        <a
                            href="{{ route('register') }}"
                            class="inline-flex justify-center items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-lg"
                        >
                            🚨 Signaler un incident
                        </a>

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex justify-center items-center px-6 py-3 bg-white/95 hover:bg-white text-gray-800 font-semibold rounded-xl transition"
                        >
                            Se connecter
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         INTRO
    ========================== --}}

    <section class="py-20 bg-white">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <span class="text-green-600 font-semibold text-sm uppercase tracking-wider">
                Comment ça marche ?
            </span>

            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">
                Signaler un problème en quelques étapes
            </h2>

            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">
                Une plateforme simple pour transformer les signalements
                citoyens en actions concrètes.
            </p>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">


                {{-- Step 1 --}}
                <div class="p-8 rounded-2xl bg-green-50 border border-green-100">

                    <div class="w-14 h-14 mx-auto flex items-center justify-center bg-green-600 text-white rounded-full text-2xl font-bold">
                        1
                    </div>

                    <h3 class="text-xl font-bold mt-6">
                        Signalez
                    </h3>

                    <p class="text-gray-600 mt-3">
                        Décrivez l'incident, ajoutez une photo
                        et indiquez sa localisation.
                    </p>

                </div>


                {{-- Step 2 --}}
                <div class="p-8 rounded-2xl bg-blue-50 border border-blue-100">

                    <div class="w-14 h-14 mx-auto flex items-center justify-center bg-blue-600 text-white rounded-full text-2xl font-bold">
                        2
                    </div>

                    <h3 class="text-xl font-bold mt-6">
                        Analysez
                    </h3>

                    <p class="text-gray-600 mt-3">
                        L'assistant GeoEco peut analyser le signalement
                        et proposer une catégorie et une priorité.
                    </p>

                </div>


                {{-- Step 3 --}}
                <div class="p-8 rounded-2xl bg-orange-50 border border-orange-100">

                    <div class="w-14 h-14 mx-auto flex items-center justify-center bg-orange-500 text-white rounded-full text-2xl font-bold">
                        3
                    </div>

                    <h3 class="text-xl font-bold mt-6">
                        Suivez
                    </h3>

                    <p class="text-gray-600 mt-3">
                        Suivez l'évolution de votre incident
                        jusqu'à sa résolution.
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         FEATURES
    ========================== --}}

    <section class="py-20 bg-gray-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">

                <span class="text-green-600 font-semibold">
                    Fonctionnalités
                </span>

                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">
                    Une plateforme pensée pour l'action
                </h2>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">


                {{-- Feature 1 --}}
                <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">

                    <div class="text-4xl mb-5">
                    </div>

                    <h3 class="text-xl font-bold mb-3 text-blue-600">
                        Carte interactive
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        Visualisez les incidents géolocalisés
                        et consultez leurs informations.
                    </p>

                </div>


                {{-- Feature 2 --}}
                <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">

                    <div class="text-4xl mb-5">
                    </div>

                    <h3 class="text-xl font-bold mb-3 text-blue-600">
                        GeoEco Assistant
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        Un assistant intelligent pour aider à analyser
                        les incidents et proposer des actions.
                    </p>

                </div>


                {{-- Feature 3 --}}
                <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">

                    <div class="text-4xl mb-5">
                    </div>

                    <h3 class="text-xl font-bold mb-3 text-blue-600">
                        Notifications
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        Recevez les informations importantes
                        concernant vos signalements.
                    </p>

                </div>


                {{-- Feature 4 --}}
                <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">

                    <div class="text-4xl mb-5">
                    </div>

                    <h3 class="text-xl font-bold mb-3 text-blue-600">
                        Statistiques
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed">
                        Consultez les indicateurs permettant
                        de mieux comprendre les incidents.
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         INCIDENT IMAGE
    ========================== --}}

    <section class="py-20 bg-white">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">


                <div>

                    <span class="text-green-600 font-semibold">
                        Une action concrète
                    </span>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3 mb-6">
                        Chaque signalement peut faire la différence
                    </h2>

                    <p class="text-gray-600 leading-relaxed mb-6">
                        Déchets, pollution, éclairage public,
                        fuite d'eau, nids-de-poule ou dégradation
                        des espaces verts : signalez facilement
                        les problèmes observés dans votre ville.
                    </p>


                    <ul class="space-y-4 text-gray-700">

                        <li class="flex gap-3">
                            <span class="text-green-600 font-bold">✓</span>
                            Localisation précise de l'incident
                        </li>

                        <li class="flex gap-3">
                            <span class="text-green-600 font-bold">✓</span>
                            Ajout de photos
                        </li>

                        <li class="flex gap-3">
                            <span class="text-green-600 font-bold">✓</span>
                            Suivi du statut
                        </li>

                        <li class="flex gap-3">
                            <span class="text-green-600 font-bold">✓</span>
                            Communication avec les équipes
                        </li>

                    </ul>

                </div>


                <div class="rounded-2xl overflow-hidden shadow-xl">

                    <img
                        src="{{ asset('images/geeco/incident-card.jpg') }}"
                        alt="Intervention sur un incident"
                        class="w-full h-[380px] object-cover"
                    >

                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         CTA
    ========================== --}}

    <section class="py-20 bg-green-700">

        <div class="max-w-4xl mx-auto px-4 text-center text-white">

            <h2 class="text-3xl md:text-4xl font-bold">
                Prêt à contribuer à une ville meilleure ?
            </h2>

            <p class="mt-5 text-green-100 text-lg">
                Rejoignez GeoEcoReport et participez à l'amélioration
                de votre environnement.
            </p>


            @guest

                <div class="mt-8">

                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center px-7 py-3 bg-white text-green-700 font-bold rounded-xl hover:bg-gray-100 transition"
                    >
                        Créer mon compte
                    </a>

                </div>

            @endguest

        </div>

    </section>


    {{-- =========================
         FOOTER
    ========================== --}}

    <footer class="bg-gray-900 text-gray-300">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <div class="flex flex-col md:flex-row justify-between gap-6">

                <div>

                    <div class="text-xl font-bold text-white">
                        GeoEcoReport
                    </div>

                    <p class="text-sm text-gray-400 mt-2">
                        Plateforme intelligente de signalement
                        des incidents urbains et environnementaux.
                    </p>

                </div>


                <div class="text-sm text-gray-400">
                    © {{ date('Y') }} GeoEcoReport.
                    Tous droits réservés.
                </div>

            </div>

        </div>

    </footer>

</body>

</html>