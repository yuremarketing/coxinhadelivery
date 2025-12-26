<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega - Pedido #{{ $pedido->numero_pedido }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white font-sans">

    <div class="max-w-md mx-auto p-4">
        <div class="text-center mb-6">
            <i class="fas fa-motorcycle text-orange-500 text-4xl mb-2"></i>
            <h1 class="text-2xl font-bold">Entrega Coxinha</h1>
            <p class="text-gray-400">Pedido #{{ $pedido->numero_pedido }}</p>
        </div>

        <div class="bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-700 mb-4">
            <div class="mb-4">
                <label class="text-gray-400 text-sm uppercase">Cliente</label>
                <p class="text-xl font-semibold">{{ $pedido->cliente_nome }}</p>
            </div>

            <div class="mb-4">
                <label class="text-gray-400 text-sm uppercase">Endereço de Entrega</label>
                <p class="text-lg text-orange-200">{{ $pedido->observacoes ?? 'Endereço não detalhado' }}</p>
            </div>

            <div class="mb-6">
                <label class="text-gray-400 text-sm uppercase">Valor a Receber</label>
                <p class="text-3xl font-bold text-green-400">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
            </div>

            @php
                // Criamos o link de busca do Google Maps baseado no endereço
                // O urlencode serve para transformar espaços em caracteres que a URL entende
                $enderecoBusca = urlencode($pedido->observacoes); 
                $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$enderecoBusca}";
            @endphp

            <a href="{{ $googleMapsUrl }}" target="_blank" 
               class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition mb-3">
                <i class="fas fa-map-marker-alt mr-2"></i>
                ABRIR NO GOOGLE MAPS
            </a>

            <a href="tel:{{ $pedido->cliente_telefone }}" 
               class="flex items-center justify-center w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-4 rounded-xl transition">
                <i class="fas fa-phone-alt mr-2"></i>
                LIGAR PARA CLIENTE
            </a>
        </div>

        <div class="text-center">
            <span class="px-4 py-2 rounded-full bg-orange-500/20 text-orange-500 border border-orange-500/50">
                Status: {{ $pedido->status }}
            </span>
        </div>
    </div>

</body>
</html>
