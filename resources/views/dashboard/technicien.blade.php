<!-- المكون الرئيسي للهيكل العام للتطبيق (Layout) -->
<x-app-layout>

    <!-- شريط العنوان العلوي (Header Slot) -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Technicien
        </h2>
    </x-slot>

    <!-- حاوية المحتوى الرئيسي (Main Content Container) -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- البطاقة البيضاء لعرض البيانات (Content Card) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- نص الترحيب بالتقني -->
                Bienvenue dans votre espace technicien.
            </div>
        </div>
    </div>

</x-app-layout>