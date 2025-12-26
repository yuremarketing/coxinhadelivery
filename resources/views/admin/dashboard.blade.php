@extends('layouts.admin')

@section('conteudo')
<h1 class="text-3xl font-black text-slate-800 uppercase mb-8">📊 Painel de Controle</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
        <span class="block text-xs font-bold text-slate-400 uppercase">Faturamento Hoje</span>
        <span class="text-3xl font-black text-slate-800">R$ {{ number_format($vendasHoje, 2, ',', '.') }}</span>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-orange-500">
        <span class="block text-xs font-bold text-slate-400 uppercase">Pedidos Hoje</span>
        <span class="text-3xl font-black text-slate-800">{{ $pedidosHoje }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="font-black text-slate-800 uppercase mb-4 border-b pb-2">🔥 Mais Vendidos</h3>
        @foreach($maisVendidos as $item)
        <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
            <span class="font-bold text-slate-700">{{ $item->produto->nome }}</span>
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-bold">{{ (int)$item->total }} un.</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
