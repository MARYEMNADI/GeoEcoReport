<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Catégories
            </h2>

            @can('create', App\Models\Category::class)
                <a href="{{ route('categories.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    + Ajouter une catégorie
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">

                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                                Nom
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                                Type
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                                Description
                            </th>

                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($categories as $category)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $category->name }}
                                </td>

                                <td class="px-6 py-4">

                                    @if($category->type === 'Urbain')

                                        <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                                            Urbain
                                        </span>

                                    @else

                                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                                            Environnemental
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $category->description ?? 'Aucune description' }}
                                </td>

                                <td class="px-6 py-4 text-right">

                                    <div class="flex justify-end gap-2">

                                        @can('update', $category)

                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                                Modifier
                                            </a>

                                        @endcan


                                        @can('delete', $category)

                                            <form method="POST"
                                                  action="{{ route('categories.destroy', $category) }}">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        onclick="return confirm('Voulez-vous supprimer cette catégorie ?')"
                                                        class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                                    Supprimer
                                                </button>

                                            </form>

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="px-6 py-10 text-center text-gray-500">

                                    Aucune catégorie trouvée.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>

        </div>
    </div>

</x-app-layout>