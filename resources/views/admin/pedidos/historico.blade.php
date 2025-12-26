@extends('layouts.admin')

@section('conteudo')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">📊 Relatório de Vendas</h1>
        <p class="text-slate-500">Acompanhe o desempenho do seu delivery</p>
    </div>
    <div class="bg-white border-2 border-green-500 p-4 rounded-2xl shadow-sm text-right">
        <span class="block text-xs text-green-600 font-bold uppercase">Faturamento Total</span>
        <span class="text-2xl font-black text-slate-800">R$ {{ number_format(\App\Models\Pedido::where('status', 'finalizado')->sum('valor_total'), 2, ',', '.') }}</span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="p-4 font-bold text-slate-600">Pedido</th>
                <th class="p-4 font-bold text-slate-600">Cliente</th>
                <th class="p-4 font-bold text-slate-600">Data/Hora</th>
                <th class="p-4 font-bold text-slate-600">Valor</th>
                <th class="p-4 font-bold text-slate-600">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\Pedido::where('status', 'finalizado')->orderBy('updated_at', 'desc')->get() as $pedido)
            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="p-4 font-mono font-bold text-orange-600">{{ $pedido->numero_pedido }}</td>
                <td class="p-4">
                    <span class="block font-bold text-slate-800">{{ $pedido->cliente_nome }}</span>
                    <span class="text-xs text-slate-500">{{ $pedido->cliente_telefone }}</span>
                </td>
                <td class="p-4 text-slate-600 text-sm">
                    {{ $pedido->updated_at->format('d/m/Y H:i') }}
                </td>
                <td class="p-4 font-bold text-slate-800">
                    R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                </td>
                <td class="p-4">
                    <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="pendente">
                        <button type="submit" class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-1 px-3 rounded-lg transition">
                            REABRIR
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
