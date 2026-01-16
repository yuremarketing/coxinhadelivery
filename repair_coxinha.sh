#!/bin/bash

echo "🚑 Iniciando Protocolo de Reparo..."

# 1. CONSERTAR PERMISSÕES (Resolve o Erro 500)
echo "🔧 Ajustando permissões de pastas (Storage e Cache)..."
docker compose exec -u root laravel.test chmod -R 777 storage bootstrap/cache
docker compose exec laravel.test php artisan optimize:clear

# 2. HABILITAR MODO DE DEBUG (Para vermos erros reais se acontecerem)
# Altera o arquivo .env para APP_DEBUG=true
if grep -q "APP_DEBUG=false" .env; then
   sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env
   echo "🐛 Modo Debug ATIVADO."
else
   echo "ℹ️  Modo Debug já estava ativo ou não encontrado."
fi

# 3. REFAZER A CONFIGURAÇÃO DO REACT (Pois os arquivos voltaram ao padrão)
echo "🎨 Reescrevendo arquivos do Frontend React..."

# A) Configurar Vite para React
cat << 'EOF' > vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
});
EOF

# B) Recriar o Cardápio (Componente Principal)
mkdir -p resources/js/components
cat << 'EOF' > resources/js/components/Cardapio.jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';

export default function Cardapio() {
    const [produtos, setProdutos] = useState([]);

    useEffect(() => {
        axios.get('/api/produtos').then(res => setProdutos(res.data));
    }, []);

    return (
        <div className="min-h-screen bg-gray-50 pb-20 font-sans">
            <header className="bg-orange-600 p-4 shadow-md sticky top-0 z-10">
                <div className="container mx-auto flex justify-between items-center text-white">
                    <h1 className="text-xl font-bold flex items-center gap-2">
                        🍗 Coxinha Delivery
                    </h1>
                    <a href="/login" className="text-sm bg-orange-700 px-3 py-1 rounded hover:bg-orange-800">
                        Sou Dono
                    </a>
                </div>
            </header>

            <main className="container mx-auto p-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    {produtos.map(p => (
                        <div key={p.id} className="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                            <div className="h-48 bg-gray-200 w-full relative">
                                {p.imagem ? (
                                    <img 
                                        src={p.imagem.startsWith('http') ? p.imagem : '/storage/'+p.imagem} 
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-gray-400">Sem Foto</div>
                                )}
                            </div>
                            <div className="p-4 flex flex-col flex-grow">
                                <h2 className="font-bold text-lg text-gray-800">{p.nome}</h2>
                                <p className="text-green-600 font-bold text-xl mt-1">R$ {p.preco}</p>
                                <button className="mt-auto w-full bg-orange-500 text-white font-bold py-3 rounded-lg hover:bg-orange-600 shadow-sm mt-3">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </main>
        </div>
    );
}
EOF

# C) Recriar o Ponto de Entrada (App.jsx)
cat << 'EOF' > resources/js/app.jsx
import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import Cardapio from './components/Cardapio';

if(document.getElementById('app')){
    ReactDOM.createRoot(document.getElementById('app')).render(<Cardapio />);
}
EOF

# D) Atualizar a View do Laravel
cat << 'EOF' > resources/views/welcome.blade.php
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Coxinha Delivery</title>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="bg-gray-100">
        <div id="app"></div>
    </body>
</html>
EOF

echo "✅ Reparo Concluído!"
echo "Tente rodar 'npm run dev' novamente e recarregar a página."
