<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GeoEcoReport</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

    <div class="min-h-screen">

        <img
            src="{{ asset('images/geeco/hero-environment.jpg') }}"
            alt="GeoEcoReport"
            class="h-80 w-full object-cover"
        >

        <div class="mx-auto max-w-5xl px-6 py-10">

            <img
                src="{{ asset('images/geeco/logo-mark.png') }}"
                alt="Logo GeoEcoReport"
                class="h-24 w-24 object-contain"
            >

            <h1 class="mt-6 text-4xl font-bold text-slate-900">
                GeoEcoReport
            </h1>

            <p class="mt-3 text-lg text-slate-600">
                Plateforme de signalement des incidents urbains et environnementaux.
            </p>

            <img
                src="{{ asset('images/geeco/incident-card.jpg') }}"
                alt="Incident urbain"
                class="mt-8 h-64 w-full rounded-2xl object-cover shadow-lg"
            >

        </div>

    </div>

</body>
</html>
