import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';

/**
 * ARQUIVO DE ENTRADA DO REACT (O Big Bang)
 * ---------------------------------------------------
 * Eu configurei este arquivo para ser o ponto de partida do Frontend.
 * O Laravel carrega este script, e este script carrega todo o resto do site.
 */

function App() {
    return (
        // Decidi usar estilos inline provisórios só pra validar a instalação.
        // O Designer (ou eu mesmo) vai tirar isso depois e usar CSS de verdade.
        <div style={{ textAlign: 'center', marginTop: '50px', fontFamily: 'Arial' }}>
            <h1>🍗 Coxinha Delivery</h1>
            <p>Status do Sistema: O React assumiu o controle do navegador!</p>
            <small>Se você está lendo isso, a integração Laravel + Vite + React funcionou.</small>
        </div>
    );
}

// LÓGICA DE MONTAGEM (The Mounting Logic)
// ----------------------------------------
// Eu procuro no HTML principal (welcome.blade.php) uma DIV vazia com id="app".
// É como se eu estivesse alugando um terreno vazio pra construir a loja.
const rootElement = document.getElementById('app');

if (rootElement) {
    // Achei o terreno! Agora mando o React construir a interface lá dentro.
    const root = createRoot(rootElement);
    root.render(<App />);
} else {
    // Se der erro aqui, é porque alguém mexeu no HTML e tirou a div 'app'.
    console.error('ERRO CRÍTICO: Não encontrei a div id="app" no HTML. O React não tem onde morar.');
}
