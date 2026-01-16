#!/bin/bash

echo "📱 Iniciando Configuração Frontend (Mobile First)..."

# 1. INSTALAR DEPENDÊNCIAS (React + Router + Axios)
echo "📦 Instalando pacotes (pode levar 1-2 min)..."
docker compose exec laravel.test npm install react react-dom @vitejs/plugin-react react-router-dom axios --save-dev

# 2. CONFIGURAR VITE (O Compilador)
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

# 3. CONFIGURAR TAILWIND (Estilos)
cat << 'EOF' > resources/css/app.css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Ajuste extra para rolagem suave no celular */
html { scroll-behavior: smooth; }
EOF

# 4. CRIAR ESTRUTURA DE PASTAS
mkdir -p resources/js/components

# --- Componente 1: CARDÁPIO (A Loja) ---
# Note as classes: 'grid-cols-1' (celular) e 'md:grid-cols-3' (PC)
cat << 'EOF' > resources/js/components/Cardapio.jsx
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

export default function Cardapio() {
    const [produtos, setProdutos] = useState([]);
    
    useEffect(() => {
        axios.get('/api/produtos').then(res => setProdutos(res.data));
    }, []);

    return (
        <div className="min-h-screen bg-gray-50 pb-20">
            {/* Cabeçalho Mobile-Friendly */}
            <header className="bg-orange-600 p-4 shadow-md sticky top-0 z-10">
                <div className="container mx-auto flex justify-between items-center text-white">
                    <h1 className="text-xl font-bold flex items-center gap-2">
                        🍗 Coxinha Delivery
                    </h1>
                    <Link to="/login" className="text-sm bg-orange-700 px-3 py-1 rounded hover:bg-orange-800">
                        Sou Dono
                    </Link>
                </div>
            </header>

            {/* Lista de Produtos */}
            <main className="container mx-auto p-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {produtos.map(p => (
                        <div key={p.id} className="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                            {/* Imagem Responsiva */}
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
                                <p className="text-green-600 font-bold text-xl mt-1">
                                    R$ {parseFloat(p.preco).toFixed(2).replace('.', ',')}
                                </p>
                                
                                {/* Botão Grande para facilitar o toque no celular */}
                                <button className="mt-auto w-full bg-orange-500 text-white font-bold py-3 rounded-lg active:bg-orange-700 hover:bg-orange-600 transition shadow-sm mt-3">
                                    Adicionar (+1)
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

# --- Componente 2: LOGIN ---
cat << 'EOF' > resources/js/components/Login.jsx
import React, { useState } from 'react';
import axios from 'axios';

export default function Login() {
    const [email, setEmail] = useState('admin@coxinha.com');
    const [password, setPassword] = useState('password');

    const handleLogin = async (e) => {
        e.preventDefault();
        try {
            const res = await axios.post('/api/login', { email, password });
            localStorage.setItem('token', res.data.access_token);
            window.location.href = '/admin';
        } catch (error) {
            alert('Erro ao entrar. Verifique os dados.');
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100 p-4">
            <form onSubmit={handleLogin} className="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                <h2 className="text-2xl mb-6 font-bold text-center text-gray-700">Área Restrita</h2>
                <div className="mb-4">
                    <label className="block text-gray-600 mb-1">Email</label>
                    <input className="w-full border p-3 rounded-lg" type="email" value={email} onChange={e => setEmail(e.target.value)} />
                </div>
                <div className="mb-6">
                    <label className="block text-gray-600 mb-1">Senha</label>
                    <input className="w-full border p-3 rounded-lg" type="password" value={password} onChange={e => setPassword(e.target.value)} />
                </div>
                <button className="w-full bg-blue-600 text-white p-3 rounded-lg font-bold text-lg">Entrar</button>
            </form>
        </div>
    );
}
EOF

# --- Componente 3: PAINEL ADMIN (Responsivo) ---
cat << 'EOF' > resources/js/components/AdminPanel.jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';

export default function AdminPanel() {
    const [pedidos, setPedidos] = useState([]);

    const carregar = async () => {
        const token = localStorage.getItem('token');
        if(!token) return window.location.href = '/login';
        const res = await axios.get('/api/admin/pedidos', {headers: {Authorization: `Bearer ${token}`}});
        setPedidos(res.data);
    };

    useEffect(() => { carregar(); }, []);

    return (
        <div className="min-h-screen bg-gray-100 p-4">
            <div className="max-w-4xl mx-auto">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Painel do Dono</h1>
                    <button onClick={carregar} className="bg-white border px-4 py-2 rounded shadow text-sm">Atualizar</button>
                </div>

                <div className="space-y-4">
                    {pedidos.map(p => (
                        <div key={p.id} className="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                            <div className="flex justify-between items-start">
                                <div>
                                    <span className="font-bold text-lg">Pedido #{p.id}</span>
                                    <p className="text-gray-500 text-sm">{new Date(p.created_at).toLocaleString()}</p>
                                    <p className="mt-1 font-semibold">{p.user ? p.user.name : 'Cliente'}</p>
                                </div>
                                <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase ${
                                    p.status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 
                                    p.status === 'em_preparacao' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                                }`}>
                                    {p.status.replace('_', ' ')}
                                </span>
                            </div>
                        </div>
                    ))}
                    {pedidos.length === 0 && <p className="text-center text-gray-500">Nenhum pedido hoje.</p>}
                </div>
            </div>
        </div>
    );
}
EOF

# 5. PONTO DE ENTRADA (Rotas)
cat << 'EOF' > resources/js/app.jsx
import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Cardapio from './components/Cardapio';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';

ReactDOM.createRoot(document.getElementById('app')).render(
    <BrowserRouter>
        <Routes>
            <Route path="/" element={<Cardapio />} />
            <Route path="/login" element={<Login />} />
            <Route path="/admin" element={<AdminPanel />} />
        </Routes>
    </BrowserRouter>
);
EOF

# 6. ENTRADA LARAVEL
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

echo "✅ Frontend React Mobile-First Instalado!"
echo "⚠️  IMPORTANTE: Para ver o site, rode o comando abaixo em outro terminal:"
echo "docker compose exec laravel.test npm run dev"
