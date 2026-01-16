#!/bin/bash

echo "🏗️ [Coxinha Delivery] - Fase 80: Novo Projeto Front-end"

# Passo 1: Limpar arquivos de front-end antigos para evitar conflitos
# Removemos o app.jsx e o app.css velhos
rm -rf resources/js/*
rm -rf resources/css/*

# Passo 2: Criar a pasta do provedor do React
mkdir -p resources/js/Components

# Passo 3: Criar um app.jsx NOVO e LIMPO
cat << 'EOF' > resources/js/app.jsx
import './bootstrap';
import '../css/app.css';

import React from 'react';
import { createRoot } from 'react-dom/client';

const App = () => {
    return (
        <div style={{ 
            display: 'flex', 
            flexDirection: 'column', 
            alignItems: 'center', 
            justifyContent: 'center', 
            height: '100vh',
            fontFamily: 'sans-serif',
            backgroundColor: '#ff9800',
            color: 'white'
        }}>
            <h1>🚀 Coxinha Delivery - Front-end Novo</h1>
            <p>O ambiente Docker está estável e o Vite está rodando!</p>
            <div style={{ background: 'white', color: 'black', padding: '20px', borderRadius: '10px' }}>
                Status da API: <strong>Pronto para conectar</strong>
            </div>
        </div>
    );
};

const container = document.getElementById('app');
const root = createRoot(container);
root.render(<App />);
EOF

# Passo 4: Criar um CSS básico
echo "body { margin: 0; padding: 0; }" > resources/css/app.css

echo "------------------------------------------------"
echo "✅ FRONT-END RESETADO E PRONTO!"
echo "------------------------------------------------"
echo "PARA VER O NOVO FRONT:"
echo "1. Garanta que o motor está ligado: docker compose exec laravel.test npm run dev -- --host 0.0.0.0"
echo "2. No Chrome, abra http://localhost"
echo "------------------------------------------------"
