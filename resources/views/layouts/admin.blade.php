<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coxinha Delivery - Admin</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50">
    <nav class="bg-orange-600 text-white p-3 shadow-xl mb-6 no-print">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-white/20 p-2 rounded-xl">
                    <i class="fas fa-motorcycle text-xl"></i>
                </div>
                <span class="font-black text-lg uppercase tracking-tighter hidden sm:block">COXINHA DELIVERY</span>
            </div>
            
            <div class="flex gap-3 md:gap-8 font-black text-[10px] md:text-xs items-center uppercase">
                <a href="{{ url('/admin/painel') }}" 
                   class="transition-all {{ Request::is('admin/painel*') ? 'text-yellow-300 scale-110' : 'text-white/80 hover:text-white' }}">
                   Painel
                </a>
                
                <a href="{{ url('/admin/pedidos') }}" 
                   class="transition-all {{ Request::is('admin/pedidos') ? 'text-yellow-300 scale-110' : 'text-white/80 hover:text-white' }}">
                   Cozinha
                </a>

                <a href="{{ url('/admin/pedidos/historico') }}" 
                   class="transition-all {{ Request::is('admin/pedidos/historico*') ? 'text-yellow-300 scale-110' : 'text-white/80 hover:text-white' }}">
                   Relatórios
                </a>

                <a href="{{ url('/admin/produtos') }}" 
                   class="transition-all {{ Request::is('admin/produtos*') ? 'text-yellow-300 scale-110' : 'text-white/80 hover:text-white' }}">
                   Produtos
                </a>
                
                <div id="config-menu-react" class="ml-4" class="min-w-[40px] min-h-[40px] flex items-center justify-center"></div>
                
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4">
        @yield('conteudo')
    </main>
</body>
</html>
