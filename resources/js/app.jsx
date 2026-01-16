import './bootstrap';
import '../css/app.css';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

const App = () => {
    const [coxinhas, setCoxinhas] = useState([]);

    // Este useEffect vai buscar os dados da sua API assim que a tela carregar
    useEffect(() => {
        fetch('/api/coxinhas') // Ajuste para a sua rota real de API
            .then(res => res.json())
            .then(data => setCoxinhas(data))
            .catch(err => console.log("Aguardando API...", err));
    }, []);

    return (
        <div style={{ backgroundColor: '#ff9800', minHeight: '100vh', color: 'white', padding: '20px', textAlign: 'center' }}>
            <h1>🚀 Coxinha Delivery - Painel de Controle</h1>
            <div style={{ background: 'white', color: 'black', padding: '20px', borderRadius: '15px', display: 'inline-block' }}>
                <h3>Lista de Pedidos (API)</h3>
                {coxinhas.length > 0 ? (
                    <ul>
                        {coxinhas.map(c => <li key={c.id}>{c.nome}</li>)}
                    </ul>
                ) : (
                    <p>Conectado ao motor! Buscando dados da API...</p>
                )}
            </div>
        </div>
    );
};

const root = createRoot(document.getElementById('app'));
root.render(<App />);
