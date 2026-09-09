<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajouter une catégorie
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-3xl mx-auto px-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                <form method="POST" action="{{ route('categories.store') }}">

                    @csrf

                    {{-- Nom --}}
                    <div class="mb-6">

                        <label for="name"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de la catégorie
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            placeholder="Ex : Trous dans la route"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Type --}}
                    <div class="mb-6">

                        <label for="type"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Type
                        </label>

                        <select
                            id="type"
                            name="type"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">

                            <option value="">
                                Sélectionner un type
                            </option>

                            <option value="Urbain"
                                {{ old('type') === 'Urbain' ? 'selected' : '' }}>
                                Urbain
                            </option>

                            <option value="Environnemental"
                                {{ old('type') === 'Environnemental' ? 'selected' : '' }}>
                                Environnemental
                            </option>

                        </select>

                        @error('type')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Description --}}
                    <div class="mb-6">

                        <label for="description"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            placeholder="Description de la catégorie..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Actions --}}
                    <div class="flex justify-end gap-3">

                        <a href="{{ route('categories.index') }}"
                           class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700">

                            Enregistrer

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>