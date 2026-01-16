<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor da Cozinha 👨‍🍳</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="30"> </head>
<body class="bg-gray-900 text-white p-6">

    <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
        <div>
            <h1 class="text-4xl font-black text-yellow-400 uppercase tracking-widest">Cozinha</h1>
            <p class="text-gray-400 mt-1">Monitor de Preparo em Tempo Real</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold" id="relogio">--:--</div>
            <div class="text-sm text-gray-500">Atualização Automática</div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg mb-6 text-center font-bold text-xl animate-bounce">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($pedidos->isEmpty())
        <div class="flex flex-col items-center justify-center h-96 text-gray-600">
            <svg class="w-24 h-24 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-2xl font-light">Tudo tranquilo por aqui...</p>
            <p class="text-sm">Aguardando novos pedidos.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($pedidos as $pedido)
            <div class="bg-gray-800 rounded-2xl overflow-hidden border-l-8 {{ $pedido->status == 'preparando' ? 'border-yellow-500' : 'border-red-500' }} shadow-2xl relative">
                
                <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center">
                    <span class="font-mono text-2xl font-bold text-gray-400">#{{ $pedido->numero }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $pedido->status == 'preparando' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300' }}">
                        {{ $pedido->status }}
                    </span>
                </div>

                <div class="p-6">
                    <h2 class="text-2xl font-black text-white mb-2 leading-tight">
                        {{ $pedido->produto ? $pedido->produto->nome : 'Produto Removido' }}
                    </h2>
                    <p class="text-gray-400 text-sm mb-4">
                        Cliente: <strong class="text-white">{{ $pedido->cliente }}</strong>
                    </p>
                    <p class="text-xs text-gray-500 font-mono">
                        Recebido às: {{ $pedido->created_at->format('H:i') }}
                    </p>
                </div>

                <div class="p-4 bg-gray-900/50">
                    <form action="{{ route('pedidos.concluir', $pedido->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                            <span>Concluir Pedido</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <script>
        // Relógio simples
        setInterval(() => {
            const now = new Date();
            document.getElementById('relogio').innerText = now.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        }, 1000);
    </script>
</body>
</html>
