<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciar Produtos') }}
            </h2>
            <a href="{{ route('produtos.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                + Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b p-2">Foto</th>
                                <th class="border-b p-2">Nome</th>
                                <th class="border-b p-2">Preço</th>
                                <th class="border-b p-2">Estoque</th>
                                <th class="border-b p-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produtos as $produto)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b p-2">
                                    @if($produto->imagem)
                                        <img src="{{ asset('storage/' . $produto->imagem) }}" class="w-16 h-16 object-cover rounded">
                                    @else
                                        <span class="text-gray-400">Sem foto</span>
                                    @endif
                                </td>
                                <td class="border-b p-2">{{ $produto->nome }}</td>
                                <td class="border-b p-2">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                <td class="border-b p-2">{{ $produto->estoque }}</td>
                                <td class="border-b p-2 flex space-x-2">
                                    <a href="{{ route('produtos.edit', $produto->id) }}" class="text-blue-600 hover:text-blue-900 font-bold">
                                        Editar
                                    </a>

                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" onsubmit="return confirm('Tem certeza que quer apagar a {{ $produto->nome }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold ml-4">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
