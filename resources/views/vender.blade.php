@extends('layouts.monitor')
@section('title', 'Balcão - API Mode')
@section('content')
<div class="container mx-auto p-6" x-data="vendaApp()">
    <div class="flex items-center gap-2 mb-4">
        <span class="bg-orange-600 text-white text-xs px-2 py-1 rounded uppercase font-bold tracking-widest">Ambiente: Frente de Loja (API)</span>
    </div>
    <div class="bg-white p-8 rounded-3xl shadow-2xl border-2 border-slate-100">
        <h1 class="text-4xl font-black text-slate-800 mb-8 uppercase tracking-tighter">Novo Pedido</h1>
        <template x-if="mensagemSucesso">
            <div class="bg-green-500 text-white p-4 mb-6 rounded-xl animate-pulse font-bold shadow-lg" x-text="mensagemSucesso"></div>
        </template>
        <form @submit.prevent="enviarPedido">
            <div class="mb-6">
                <label class="block text-slate-400 text-sm font-bold mb-2 uppercase italic">Quem está pedindo?</label>
                <input type="text" x-model="form.cliente_nome" placeholder="Nome do cliente..." required
                    class="w-full p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-orange-500 outline-none font-bold text-lg transition-all shadow-inner">
            </div>
            <div class="grid grid-cols-1 gap-4 mb-8">
                @foreach($produtos as $produto)
                    <label class="relative flex items-center p-4 bg-slate-50 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-100 transition-all">
                        <input type="radio" name="produto_id" value="{{ $produto->id }}" x-model="form.produto_id" class="w-6 h-6 accent-orange-500" required>
                        <div class="ml-4 flex-1">
                            <p class="font-black text-slate-800 text-lg uppercase">{{ $produto->nome }}</p>
                            <p class="text-orange-600 font-bold italic">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            <button type="submit" :disabled="enviando"
                class="w-full bg-slate-800 hover:bg-black text-white font-black py-5 rounded-2xl transition-all shadow-xl uppercase text-xl tracking-widest disabled:opacity-50 flex items-center justify-center gap-3">
                <span x-text="enviando ? 'PROCESSANDO...' : '🚀 ENVIAR PARA API'"></span>
            </button>
        </form>
    </div>
</div>
<script>
function vendaApp() {
    return {
        form: { cliente_nome: '', produto_id: null },
        enviando: false,
        mensagemSucesso: '',
        async enviarPedido() {
            this.enviando = true;
            try {
                const response = await fetch('/api/pedidos', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                const result = await response.json();
                if (response.ok) {
                    this.mensagemSucesso = `✅ Sucesso! Pedido #${result.data.numero} de ${result.data.cliente} enviado.`;
                    this.form.cliente_nome = ''; this.form.produto_id = null;
                    setTimeout(() => this.mensagemSucesso = '', 5000);
                } else { alert('Erro na API: ' + (result.message || 'Verifique os dados')); }
            } catch (error) { console.error('Erro:', error); alert('Erro ao conectar com a API');
            } finally { this.enviando = false; }
        }
    }
}
</script>
@endsection
