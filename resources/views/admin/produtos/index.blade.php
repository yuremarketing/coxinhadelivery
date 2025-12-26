@extends('layouts.admin')

@section('conteudo')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
        <h2 class="text-xl font-black text-slate-800 uppercase mb-4">Novo Produto</h2>
        {{-- AQUI ESTÁ O SEGREDO: A ROTA CORRETA É admin.produtos.store --}}
        <form action="{{ route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase">Nome</label>
                <input type="text" name="nome" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase">Preço</label>
                <input type="number" name="preco" step="0.01" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase">Foto do Produto</label>
                <input type="file" name="imagem" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </div>
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition">SALVAR</button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 font-bold text-slate-600 uppercase text-xs">Foto</th>
                    <th class="p-4 font-bold text-slate-600 uppercase text-xs">Produto</th>
                    <th class="p-4 font-bold text-slate-600 uppercase text-xs">Preço</th>
                    <th class="p-4 font-bold text-slate-600 uppercase text-xs">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtos as $produto)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                    <td class="p-4">
                        @if($produto->imagem)
                            {{-- Ajustamos o caminho da imagem para ser visto pelo navegador --}}
                            <img src="{{ asset('storage/' . $produto->imagem) }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                        @else
                            <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center text-[10px] text-slate-400 border border-dashed border-slate-300">SEM FOTO</div>
                        @endif
                    </td>
                    <td class="p-4 font-bold text-slate-800">{{ $produto->nome }}</td>
                    <td class="p-4 text-slate-600 font-mono">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td class="p-4">
                        <form action="{{ route('admin.produtos.destroy', $produto->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 font-bold text-xs uppercase hover:underline">Remover</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
