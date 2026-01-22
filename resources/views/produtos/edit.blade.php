<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar: {{ $produto->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('produtos.update', $produto->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nome" :value="__('Nome')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" value="{{ $produto->nome }}" required />
                        </div>

                        <div>
                            <x-input-label for="preco" :value="__('Preço')" />
                            <x-text-input id="preco" class="block mt-1 w-full" type="number" step="0.01" name="preco" value="{{ $produto->preco }}" required />
                        </div>

                        <div>
                            <x-input-label for="estoque" :value="__('Estoque')" />
                            <x-text-input id="estoque" class="block mt-1 w-full" type="number" name="estoque" value="{{ $produto->estoque }}" required />
                        </div>

                        <div>
                            <x-input-label for="imagem" :value="__('Trocar Foto (Opcional)')" />
                            <input id="imagem" type="file" name="imagem" class="block mt-1 w-full border border-gray-300 rounded-md p-2">
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button class="ml-4 bg-yellow-500 hover:bg-yellow-600">
                                {{ __('Atualizar Produto') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
