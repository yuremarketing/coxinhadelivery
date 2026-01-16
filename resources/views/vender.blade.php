<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coxinha Delivery - PDV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.fade-out { opacity: 0; transition: opacity 1s ease-out; }</style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
        
        <div class="bg-orange-600 p-6 text-center relative overflow-hidden">
            <svg class="absolute top-0 left-0 w-32 h-32 text-orange-500 transform -translate-x-8 -translate-y-8 opacity-50 z-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
            </svg>

            <div class="relative z-10">
                <svg class="w-12 h-12 mx-auto text-white mb-2 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                
                <h1 class="text-3xl font-black text-white tracking-wider uppercase drop-shadow-sm">Novo Pedido</h1>
                
                <div class="mt-2 flex items-center justify-center gap-2 text-sm font-bold bg-orange-700/40 py-1 px-4 rounded-full inline-flex mx-auto border border-orange-400/30 shadow-inner">
                    @auth
                        @if(Auth::user()->is_admin)
                            <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span class="uppercase text-yellow-100 tracking-wide">Gerente: {{ Auth::user()->name }}</span>
                        @else
                            <svg class="w-5 h-5 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="uppercase text-white">Atendente: {{ Auth::user()->name }}</span>
                        @endif
                    @else
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-gray-200">PDV (Não Logado)</span>
                    @endauth
                </div>
            </div>
        </div>

        <div class="p-8">
            <form id="formPedido" class="space-y-6">
                <div>
                    <label class="block text-slate-500 text-xs font-bold uppercase tracking-wide mb-2">Quem está pedindo?</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" id="cliente" name="cliente" required 
                               class="w-full bg-slate-100 text-slate-800 border-none rounded-xl pl-10 pr-4 py-3 font-bold focus:ring-2 focus:ring-orange-500 outline-none transition" 
                               placeholder="Nome do Cliente">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-500 text-xs font-bold uppercase tracking-wide mb-2">Qual o sabor?</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($produtos as $produto)
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="produto_id" value="{{ $produto->id }}" class="peer sr-only" required>
                            
                            <div class="bg-slate-100 text-slate-600 rounded-xl p-4 text-center border-2 border-transparent peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-700 font-bold transition-all h-full flex flex-col items-center justify-center group-hover:bg-slate-200 shadow-sm peer-checked:shadow-md">
                                <div class="absolute top-2 right-2 text-orange-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm uppercase leading-tight mt-2">{{ $produto->nome }}</span>
                                <span class="text-xs text-orange-600 font-black mt-2 bg-white/80 border border-orange-200 px-3 py-1 rounded-full">
                                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" id="btnEnviar" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-black py-4 rounded-2xl shadow-lg hover:shadow-xl transform transition hover:-translate-y-1 active:scale-95 uppercase tracking-widest flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Processar Pedido</span>
                </button>
            </form>

            <div id="msgSucesso" class="hidden mt-6 bg-green-500 text-white px-4 py-4 rounded-xl text-center relative font-bold shadow-lg flex items-center justify-center gap-2 animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Pedido Enviado para a Cozinha! 🚀
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formPedido').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnEnviar');
            const msg = document.getElementById('msgSucesso');
            const textoOriginal = btn.innerHTML;
            
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ENVIANDO...';
            btn.disabled = true;
            btn.classList.add('opacity-75');

            const produtoSelecionado = document.querySelector('input[name="produto_id"]:checked');
            
            if(!produtoSelecionado) {
                alert("Selecione um produto!");
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
                btn.classList.remove('opacity-75');
                return;
            }

            const dados = {
                cliente: document.getElementById('cliente').value,
                produto_id: produtoSelecionado.value
            };

            fetch('/api/pedidos', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify(dados)
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na rede');
                return response.json();
            })
            .then(data => {
                document.getElementById('formPedido').reset();
                msg.classList.remove('hidden', 'fade-out');
                msg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    msg.classList.add('fade-out');
                    setTimeout(() => {
                        msg.classList.add('hidden');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 1000);
                }, 3000);
            })
            .catch(error => alert('Erro ao enviar. Verifique a conexão.'))
            .finally(() => {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
                btn.classList.remove('opacity-75');
            });
        });
    </script>
</body>
</html>