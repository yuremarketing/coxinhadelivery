@extends('layouts.monitor')

@section('content')
<div class="container mx-auto p-6" 
    x-data="{ 
        somAtivado: localStorage.getItem('cozinha_som') === 'true',
        precisaInteragir: true,
        pedidosCount: {{ $pedidos->count() }},
        agora: new Date(),
        
        liberarAudio() {
            let audio = new Audio('/mammamia.mp3');
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                this.precisaInteragir = false;
            }).catch(() => {
                this.precisaInteragir = true;
            });
        },

        tocarSom() {
            if (this.somAtivado) {
                new Audio('/mammamia.mp3').play().catch(() => { this.precisaInteragir = true; });
            }
        },

        // Função para calcular o tempo de espera
        tempoDecorrido(criadoEm) {
            let inicio = new Date(criadoEm);
            let diff = Math.floor((this.agora - inicio) / 60000); // Diferença em minutos
            if (diff < 1) return 'Agora mesmo';
            return 'Há ' + diff + ' min';
        },

        verificarNovosPedidos() {
            this.agora = new Date(); // Atualiza o relógio interno
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let novoCount = doc.querySelectorAll('.pedido-card').length;
                    if (novoCount > this.pedidosCount) {
                        this.tocarSom();
                    }
                    document.getElementById('lista-pedidos').innerHTML = doc.getElementById('lista-pedidos').innerHTML;
                    this.pedidosCount = novoCount;
                });
        }
    }" 
    x-init="liberarAudio(); setInterval(() => verificarNovosPedidos(), 5000)"
    @click="liberarAudio()">

    <template x-if="precisaInteragir && somAtivado">
        <div class="bg-red-600 text-white text-center p-4 rounded-b-2xl animate-pulse font-black uppercase mb-6 cursor-pointer">
            ⚠️ O NAVEGADOR BLOQUEOU O SOM. CLIQUE AQUI PARA OUVIR O MAMMA MIA!
        </div>
    </template>

    <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border-2" :class="somAtivado ? 'border-green-200' : 'border-slate-200'">
        <h1 class="text-3xl font-black text-slate-800 uppercase">Fila de Produção</h1>
        
        <button 
            @click.stop="somAtivado = !somAtivado; localStorage.setItem('cozinha_som', somAtivado); if(somAtivado) liberarAudio()" 
            :class="somAtivado ? 'bg-green-600' : 'bg-slate-400'"
            class="text-white px-8 py-4 rounded-2xl font-black transition-all shadow-lg flex items-center gap-3"
        >
            <span x-text="somAtivado ? '✅ SOM LIGADO' : '🔈 SOM DESLIGADO'"></span>
        </button>
    </div>

    <div id="lista-pedidos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($pedidos as $pedido)
            <div class="pedido-card bg-white p-6 rounded-3xl shadow-xl border-4 border-slate-100 flex flex-col justify-between" 
                 data-criado="{{ $pedido->created_at }}">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full text-xs font-black">#{{ substr($pedido->numero_pedido, -5) }}</span>
                        <span class="text-orange-600 font-bold text-xs" x-text="tempoDecorrido('{{ $pedido->created_at }}')"></span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 uppercase leading-none mb-2">{{ $pedido->cliente_nome }}</h2>
                    <p class="text-slate-500 font-bold uppercase text-xs tracking-widest mb-4">Pedido:</p>
                    <div class="bg-slate-50 rounded-2xl p-4 border-2 border-slate-100">
                        @foreach($pedido->itens as $item)
                            <p class="font-black text-slate-700 uppercase">{{ $item->produto->nome }}</p>
                        @endforeach
                    </div>
                </div>
                
                <form action="{{ route('pedidos.concluir', $pedido->id) }}" method="POST" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <button class="w-full bg-green-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-100">
                        CONCLUIR
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-3xl border-4 border-dashed border-slate-200">
                <p class="text-slate-300 font-black text-2xl uppercase">Aguardando pedidos...</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
