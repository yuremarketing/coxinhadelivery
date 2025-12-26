<div class="pedido-card bg-white rounded-2xl shadow-xl border-2 border-slate-100 overflow-hidden transform transition-all hover:scale-[1.02]">
    <div class="bg-slate-800 p-4 flex justify-between items-center">
        <span class="text-white font-mono font-bold text-lg">#{{ substr($pedido->numero_pedido, -5) }}</span><span class="text-white block text-sm font-light opacity-80">{{ $pedido->cliente_nome }}</span>
        <span class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full font-bold uppercase text-[10px]">Preparar</span>
    </div>

    <div class="p-5">
        <p class="text-xs text-slate-400 mb-2 uppercase font-bold">Itens do Pedido:</p>
        <ul class="space-y-3 mb-6">
            @foreach($pedido->itens as $item)
                <li class="flex justify-between items-center bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <span class="font-black text-slate-800 text-lg">{{ $item->quantidade }}x</span>
                    <span class="font-bold text-slate-700">{{ $item->produto->nome }}</span>
                </li>
            @endforeach
        </ul>

        <form action="{{ route('admin.pedidos.status', $pedido->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-4 rounded-xl transition-colors shadow-md uppercase tracking-wide">
                Concluir
            </button>
        </form>
    </div>
</div>
