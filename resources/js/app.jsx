mport './bootstrap';
import './bootstrap';
mport './bootstrap';
import '../css/app.css';
mport './bootstrap';
import React, { useEffect, useState } from 'react';
mport './bootstrap';
import { createRoot } from 'react-dom/client';
mport './bootstrap';

mport './bootstrap';
const App = () => {
mport './bootstrap';
    const [coxinhas, setCoxinhas] = useState([]);
mport './bootstrap';

mport './bootstrap';
    // Este useEffect vai buscar os dados da sua API assim que a tela carregar
mport './bootstrap';
    useEffect(() => {
mport './bootstrap';
        fetch('/api/coxinhas') // Ajuste para a sua rota real de API
mport './bootstrap';
            .then(res => res.json())
mport './bootstrap';
            .then(data => setCoxinhas(data))
mport './bootstrap';
            .catch(err => console.log("Aguardando API...", err));
mport './bootstrap';
    }, []);
mport './bootstrap';

mport './bootstrap';
    return (
mport './bootstrap';
        <div style={{ backgroundColor: '#ff9800', minHeight: '100vh', color: 'white', padding: '20px', textAlign: 'center' }}>
mport './bootstrap';
            <h1>🚀 Coxinha Delivery - Painel de Controle</h1>
mport './bootstrap';
            <div style={{ background: 'white', color: 'black', padding: '20px', borderRadius: '15px', display: 'inline-block' }}>
mport './bootstrap';
                <h3>Lista de Pedidos (API)</h3>
mport './bootstrap';
                {coxinhas.length > 0 ? (
mport './bootstrap';
                    <ul>
mport './bootstrap';
                        {coxinhas.map(c => <li key={c.id}>{c.nome}</li>)}
mport './bootstrap';
                    </ul>
mport './bootstrap';
                ) : (
mport './bootstrap';
                    <p>Conectado ao motor! Buscando dados da API...</p>
mport './bootstrap';
                )}
mport './bootstrap';
            </div>
mport './bootstrap';
        </div>
mport './bootstrap';
    );
mport './bootstrap';
};
mport './bootstrap';

mport './bootstrap';
const root = createRoot(document.getElementById('app'));
mport './bootstrap';
root.render(<App />);
