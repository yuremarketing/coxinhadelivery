@extends('layouts.admin')

@section('conteudo')
<div class="mb-8 flex justify-between items-center no-print">
    <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">🍳 Painel da Cozinha</h1>
    <div class="bg-orange-100 text-orange-700 px-4 py-2 rounded-full font-bold animate-pulse text-sm">
        Aguardando Novos Pedidos...
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach(\App\Models\Pedido::where('status', 'pendente')->orderBy('created_at', 'asc')->get() as $pedido)
        <div id="cupom-{{ $pedido->id }}" class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden shadow-sm p-4">
            {{-- CABEÇALHO DO CUPOM --}}
            <div class="border-b-2 border-dashed border-slate-200 pb-3 mb-3 flex justify-between items-center">
                <div>
                    <span class="block font-black text-xl text-slate-800">{{ $pedido->numero_pedido }}</span>
                    <span class="text-xs font-bold text-slate-500 uppercase">{{ $pedido->created_at->format('d/m H:i') }}</span>
                </div>
                <span class="bg-slate-800 text-white text-xs px-2 py-1 rounded font-bold uppercase">{{ $pedido->tipo }}</span>
            </div>
            
            {{-- DADOS CLIENTE --}}
            <div class="mb-4">
                <h3 class="font-black text-lg text-slate-800 uppercase">{{ $pedido->cliente_nome }}</h3>
                <p class="font-mono text-sm text-slate-600">{{ $pedido->cliente_telefone }}</p>
            </div>

            {{-- ITENS --}}
            <div class="space-y-1 mb-4 border-b border-slate-100 pb-4">
                @foreach($pedido->itens as $item)
                    <div class="flex justify-between font-bold text-slate-700">
                        <span>{{ $item->quantidade }}x {{ $item->produto->nome }}</span>
                    </div>
                @endforeach
            </div>

            @if($pedido->observacoes)
                <div class="bg-yellow-50 p-2 rounded text-sm text-yellow-900 mb-4 border border-yellow-200">
                    <strong>OBS:</strong> {{ $pedido->observacoes }}
                </div>
            @endif
            
            {{-- BOTÕES (SOMENTE NA TELA) --}}
            <div class="flex gap-2 no-print">
                <button onclick="window.print()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i> IMPRIMIR
                </button>
                <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST" class="flex-[2]">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="finalizado">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                        PRONTO
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

<script>
    setTimeout(() => { 
        if (!window.matchMedia('(print)').matches) {
            window.location.reload(); 
        }
    }, 30000);
</script>
@endsection
