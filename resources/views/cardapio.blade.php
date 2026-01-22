cat <<'EOF' > resources/views/cardapio.blade.php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coxinha Delivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <nav class="bg-red-600 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold flex items-center gap-2">
                🍗 Coxinha Delivery
            </h1>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-white bg-red-800 px-4 py-2 rounded hover:bg-red-900 font-bold">Painel Admin</a>
                @else
                    <a href="{{ route('login') }}" class="text-white text-sm hover:underline">Sou o Dono (Login)</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-yellow-500 py-16 text-center text-white shadow-md">
        <h2 class="text-5xl font-extrabold mb-4 drop-shadow-md">Fome de Coxinha?</h2>
        <p class="text-xl font-medium">As melhores do bairro, quentinhas na sua casa.</p>
    </div>

    <div class="container mx-auto p-6 -mt-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            @foreach($produtos as $produto)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1">
                <div class="h-56 overflow-hidden bg-gray-200 relative">
                    @if($produto->imagem)
                        <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400 bg-gray-100">
                            <span class="text-4xl">📷</span>
                        </div>
                    @endif
                    <div class="absolute top-0 right-0 bg-yellow-400 text-red-800 font-bold px-3 py-1 m-2 rounded-full shadow">
                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-baseline mb-2">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $produto->nome }}</h3>
                    </div>
                    
                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded uppercase tracking-wide">
                        {{ $produto->categoria ?? 'Salgado' }}
                    </span>

                    <p class="text-gray-600 mt-4 text-sm leading-relaxed h-12 overflow-hidden">
                        {{ $produto->descricao ?? 'Uma explosão de sabor a cada mordida.' }}
                    </p>

                    <a href="https://wa.me/5511999999999?text=Olá, tenho interesse na {{ $produto->nome }}" 
                       target="_blank"
                       class="block w-full mt-6 bg-green-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-600 transition flex items-center justify-center gap-2 text-center decoration-none">
                        <span>📱</span> Pedir no Zap
                    </a>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <footer class="bg-gray-800 text-gray-400 text-center p-8 mt-12">
        <p>&copy; {{ date('Y') }} Coxinha Delivery. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
EOF
