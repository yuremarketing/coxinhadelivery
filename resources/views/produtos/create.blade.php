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
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <input type="text" name="nome" id="nome" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição (Ingredientes)</label>
                            <textarea name="descricao" id="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
                                <input type="number" step="0.01" name="preco" id="preco" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="estoque" class="block text-sm font-medium text-gray-700">Quantidade em Estoque</label>
                                <input type="number" name="estoque" id="estoque" value="10" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="categoria" class="block text-sm font-medium text-gray-700">Categoria</label>
                            <select name="categoria" id="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="salgado">Salgado</option>
                                <option value="bebida">Bebida</option>
                                <option value="combo">Combo</option>
                                <option value="sobremesa">Sobremesa</option>
                            </select>
                        </div>

                        <div>
                            <label for="imagem" class="block text-sm font-medium text-gray-700">Foto do Produto</label>
                            <input type="file" name="imagem" id="imagem" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Salvar Produto
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
