<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Novo Produto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nome" :value="__('Nome do Produto')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" required autofocus />
                        </div>

                        <div>
                            <x-input-label for="descricao" :value="__('Descrição (Ingredientes)')" />
                            <textarea id="descricao" name="descricao" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="preco" :value="__('Preço (R$)')" />
                                <x-text-input id="preco" class="block mt-1 w-full" type="number" step="0.01" name="preco" required />
                            </div>

                            <div>
                                <x-input-label for="estoque" :value="__('Quantidade em Estoque')" />
                                <x-text-input id="estoque" class="block mt-1 w-full" type="number" name="estoque" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="categoria" :value="__('Categoria')" />
                            <select id="categoria" name="categoria" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Salgado">Salgado</option>
                                <option value="Bebida">Bebida</option>
                                <option value="Combo">Combo</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="imagem" :value="__('Foto do Produto')" />
                            <input id="imagem" type="file" name="imagem" class="block mt-1 w-full border border-gray-300 rounded-md p-2">
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Salvar Produto') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
