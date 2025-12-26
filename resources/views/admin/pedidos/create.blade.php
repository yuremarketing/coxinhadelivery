@extends('layouts.admin')

@section('conteudo')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Itens Disponíveis</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach(\App\Models\Produto::where('ativo', true)->get() as $produto)
                <button type="button" 
                    onclick="adicionarAoCarrinho({{ $produto->id }}, '{{ $produto->nome }}', {{ $produto->preco }})"
                    class="flex justify-between items-center p-4 border rounded-xl hover:bg-orange-50 hover:border-orange-200 transition group text-left">
                    <div>
                        <span class="block font-bold text-gray-700 group-hover:text-orange-700">{{ $produto->nome }}</span>
                        <span class="text-xs text-gray-400 font-mono">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                    </div>
                    <i class="fas fa-plus-circle text-gray-200 group-hover:text-orange-500 transition text-xl"></i>
                </button>
            @endforeach
        </div>
    </div>

    <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl border border-slate-800">
        <h2 class="text-xl font-bold mb-6 flex items-center">
            <i class="fas fa-shopping-basket mr-2 text-orange-500"></i> Carrinho
        </h2>
        <form action="{{ route('admin.pedidos.store') }}" method="POST" id="form-pedido">
            @csrf
            <div class="space-y-4 mb-6">
                <input type="text" name="cliente_nome" placeholder="Nome do Cliente" required class="w-full bg-slate-800 border-slate-700 rounded-lg px-4 py-3 text-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="cliente_telefone" placeholder="WhatsApp" required class="w-full bg-slate-800 border-slate-700 rounded-lg px-4 py-3 text-white">
                    <select name="tipo" class="bg-slate-800 border-slate-700 rounded-lg px-4 py-3 text-slate-300">
                        <option value="entrega">🛵 Entrega</option>
                        <option value="retirada">🥡 Retirada</option>
                    </select>
                </div>
                <textarea name="observacoes" placeholder="Endereço/Obs" class="w-full bg-slate-800 border-slate-700 rounded-lg px-4 py-3 h-24 text-white"></textarea>
            </div>
            <div id="lista-carrinho" class="space-y-3 mb-6 border-t border-b border-slate-800 py-4">
                <p class="text-slate-500 text-center italic text-sm py-4">Carrinho vazio.</p>
            </div>
            <div class="flex justify-between items-center text-3xl font-black mb-6">
                <span class="text-slate-400 text-lg uppercase">Total:</span>
                <span class="text-orange-500 font-mono">R$ <span id="total-valor">0,00</span></span>
            </div>
            <input type="hidden" name="valor_total" id="input-total" value="0">
            <input type="hidden" name="itens_json" id="itens-json">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-xl uppercase text-lg">Finalizar Pedido</button>
        </form>
    </div>
</div>

<script>
    let carrinho = [];
    function adicionarAoCarrinho(id, nome, preco) {
        const item = carrinho.find(i => i.id === id);
        if (item) { item.qtd++; } else { carrinho.push({ id, nome, preco, qtd: 1 }); }
        render();
    }
    function remover(idx) { carrinho.splice(idx, 1); render(); }
    function render() {
        const lista = document.getElementById('lista-carrinho');
        let total = 0;
        lista.innerHTML = carrinho.length ? '' : '<p class="text-slate-500 text-center italic text-sm py-4">Carrinho vazio.</p>';
        carrinho.forEach((item, i) => {
            total += item.preco * item.qtd;
            lista.innerHTML += `<div class="flex justify-between p-3 bg-slate-800 rounded-xl mb-2">
                <span>${item.qtd}x ${item.nome}</span>
                <button type="button" onclick="remover(${i})" class="text-red-500">X</button>
            </div>`;
        });
        document.getElementById('total-valor').innerText = total.toFixed(2).replace('.', ',');
        document.getElementById('input-total').value = total;
        document.getElementById('itens-json').value = JSON.stringify(carrinho);
    }
</script>
@endsection
