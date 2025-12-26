<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coxinha Delivery - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            nav, .no-print { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50">
    <nav class="bg-orange-600 text-white p-4 shadow-xl mb-8 no-print">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-motorcycle text-2xl"></i>
                <span class="font-black text-xl uppercase tracking-tighter">COXINHA DELIVERY</span>
            </div>
            <div class="flex gap-6 font-bold text-sm items-center">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-orange-200 transition uppercase">Painel</a>
                <a href="{{ route('admin.pedidos.create') }}" class="hover:text-orange-200 transition uppercase">Vender</a>
                <a href="{{ route('admin.pedidos.index') }}" class="hover:text-orange-200 transition uppercase">Cozinha</a>
                <a href="{{ route('admin.pedidos.historico') }}" class="hover:text-orange-200 transition uppercase text-orange-200">Relatórios</a>
                <a href="{{ route('admin.produtos.index') }}" class="hover:text-orange-200 transition uppercase border-l border-orange-400 pl-4">Produtos</a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline ml-4">
                    @csrf
                    <button type="submit" class="bg-orange-800 hover:bg-red-700 px-3 py-1 rounded transition text-[10px] uppercase font-black">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container mx-auto px-4">
        @yield('conteudo')
    </main>
</body>
</html>
