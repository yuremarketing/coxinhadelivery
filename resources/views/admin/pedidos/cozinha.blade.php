@extends('layouts.monitor')

@section('content')
<div class="container mx-auto p-6" 
    x-data="{ 
        somAtivado: localStorage.getItem('cozinha_som') === 'true',
        precisaInteragir: true,
        pedidosCount: {{ $pedidos->count() }},
        
        // Tenta destravar o áudio
        liberarAudio() {
            let audio = new Audio('/mammamia.mp3');
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                this.precisaInteragir = false; // Áudio liberado com sucesso!
                if (localStorage.getItem('cozinha_som') === null) {
                    this.somAtivado = true;
                    localStorage.setItem('cozinha_som', true);
                }
            }).catch(() => {
                this.precisaInteragir = true;
            });
        },

        tocarSom() {
            if (this.somAtivado) {
                let som = new Audio('/mammamia.mp3');
                som.play().catch(e => {
                    this.precisaInteragir = true; // Se falhou, avisa que precisa clicar
                });
            }
        },

        verificarNovosPedidos() {
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
    @click="liberarAudio()"> <template x-if="precisaInteragir && somAtivado">
        <div class="bg-red-600 text-white text-center p-4 rounded-b-2xl animate-pulse font-black uppercase tracking-widest shadow-2xl mb-6 cursor-pointer">
            ⚠️ O NAVEGADOR BLOQUEOU O SOM. CLIQUE EM QUALQUER LUGAR PARA ATIVAR O BIP!
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
            <x-pedido-card :pedido="$pedido" />
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-3xl border-4 border-dashed border-slate-200">
                <p class="text-slate-300 font-black text-2xl uppercase">Aguardando pedidos...</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
