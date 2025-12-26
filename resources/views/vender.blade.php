@extends('layouts.monitor')

@section('title', 'Balcão - API Mode')

@section('content')
<div class="min-h-screen bg-slate-50 py-12 px-4" 
     x-data="{ 
        nome: '', 
        produtoId: '', 
        enviando: false, 
        mensagem: '',
        limparFormulario() {
            this.nome = '';
            this.produtoId = '';
            document.querySelectorAll('input[type=radio]').forEach(el => el.checked = false);
        },
        enviarPedido() {
            if(!this.nome || !this.produtoId) {
                alert('Preencha o nome e escolha a coxinha!');
                return;
            }
            this.enviando = true;
            fetch('/api/pedidos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ cliente_nome: this.nome, produto_id: this.produtoId })
            })
            .then(res => res.json())
            .then(data => {
                this.mensagem = '🚀 Pedido #' + data.data.numero + ' enviado com sucesso!';
                this.limparFormulario();
                setTimeout(() => { this.mensagem = ''; }, 5000);
            })
            .catch(err => alert('Erro na API: ' + err.message))
            .finally(() => this.enviando = false);
        }
     }">
    
    <div class="max-w-2xl mx-auto">
        <div class="mb-4 inline-block bg-orange-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
            Ambiente: Frente de Loja (API)
        </div>

        <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 p-10 border border-slate-100">
            <h1 class="text-4xl font-black text-slate-800 mb-8 tracking-tighter uppercase">Novo Pedido</h1>

            <div x-show="mensagem" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-text="mensagem" 
                 class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl font-bold border border-green-100 text-center">
            </div>

            <div class="space-y-8">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Quem está pedindo?</label>
                    <input type="text" x-model="nome" placeholder="Nome do cliente..." 
                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-5 text-lg font-medium focus:border-orange-500 focus:ring-0 transition-all outline-none">
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($produtos as $produto)
                    <label class="relative flex items-center p-5 rounded-2xl border-2 cursor-pointer transition-all hover:bg-slate-50"
                           :class="produtoId == '{{ $produto->id }}' ? 'border-orange-500 bg-orange-50/30' : 'border-slate-100'">
                        <input type="radio" name="produto" value="{{ $produto->id }}" x-model="produtoId" class="hidden">
                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 mr-4 flex items-center justify-center"
                             :class="produtoId == '{{ $produto->id }}' ? 'border-orange-500' : ''">
                            <div x-show="produtoId == '{{ $produto->id }}'" class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-black text-slate-800 uppercase text-sm">{{ $produto->nome }}</h3>
                            <p class="text-orange-600 font-bold italic text-sm">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                <button @click="enviarPedido()" :disabled="enviando"
                        class="w-full bg-slate-900 text-white py-6 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-3">
                    <span x-show="!enviando">🚀 Enviar para API</span>
                    <span x-show="enviando" class="animate-pulse">Processando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
