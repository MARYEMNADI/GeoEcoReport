<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GeoEcoReport</title>
    <!-- استدعاء Tailwind CSS للعرض والتنسيق -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Connexion</h2>

        <!-- عرض الأخطاء إن وجدت -->
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- البريد الإلكتروني -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse Email
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- كلمة المرور -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mot de passe
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- زر الدخول -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                Se connecter
            </button>
        </form>
    </div>

</body>
</html>