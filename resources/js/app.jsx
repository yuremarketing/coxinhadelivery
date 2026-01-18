import './bootstrap';
import '../css/app.css';
import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom/client';
import axios from 'axios';
import Login from './Components/Login';

function App() {
    // --- ESTADOS (A Memória do App) ---
    const [token, setToken] = useState(localStorage.getItem('coxinha_token'));
    const [produtos, setProdutos] = useState([]);
    const [loading, setLoading] = useState(false);
    const [carrinho, setCarrinho] = useState([]); // A variável que estava faltando!
    const [enviandoPedido, setEnviandoPedido] = useState(false);

    // --- EFEITOS (O que acontece ao carregar) ---
    useEffect(() => {
        if (token && token !== 'undefined') {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            carregarProdutos();
        }
    }, [token]);

    // --- FUNÇÕES ---
    const carregarProdutos = () => {
        setLoading(true);
        axios.get('/api/produtos')
            .then(response => {
                setProdutos(response.data.data ? response.data.data : response.data);
                setLoading(false);
            })
            .catch(error => {
                console.error("Erro ao carregar:", error);
                if (error.response && error.response.status === 401) logout();
                setLoading(false);
            });
    };

    const logout = () => {
        localStorage.removeItem('coxinha_token');
        setToken(null);
        setProdutos([]);
        setCarrinho([]);
        delete axios.defaults.headers.common['Authorization'];
        window.location.reload();
    };

    const adicionarAoCarrinho = (produto) => {
        const existente = carrinho.find(item => item.produto_id === produto.id);
        if (existente) {
            setCarrinho(carrinho.map(item => item.produto_id === produto.id ? { ...item, quantidade: item.quantidade + 1 } : item));
        } else {
            setCarrinho([...carrinho, { produto_id: produto.id, nome: produto.nome, preco: parseFloat(produto.preco), quantidade: 1 }]);
        }
    };

    const removerDoCarrinho = (produtoId) => {
        setCarrinho(carrinho.filter(item => item.produto_id !== produtoId));
    };

    const calcularTotal = () => {
        return carrinho.reduce((total, item) => total + (item.preco * item.quantidade), 0);
    };

    // --- FINALIZAR PEDIDO (Versão Segura: Sem enviar preço) ---
    const finalizarPedido = () => {
        if (carrinho.length === 0) return;
        
        // Lê o token direto do navegador para garantir
        const tokenReal = localStorage.getItem('coxinha_token');

        if (!tokenReal || tokenReal === 'undefined') {
            alert("Sessão inválida. Faça login novamente.");
            logout();
            return;
        }

        setEnviandoPedido(true);

        // PAYLOAD LIMPO: Só mandamos ID e Quantidade.
        // O Backend vai olhar o preço no banco de dados (Segurança).
        const payload = {
            itens: carrinho.map(item => ({
                produto_id: item.produto_id,
                quantidade: item.quantidade
            }))
        };

        axios.post('/api/pedidos', payload, {
            headers: {
                'Authorization': `Bearer ${tokenReal}`,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            alert("✅ SUCESSO! Pedido realizado! ID: " + (response.data.id || response.data.pedido_id || 'Novo'));
            setCarrinho([]); 
            setEnviandoPedido(false);
        })
        .catch(error => {
            console.error("Erro no pedido:", error);
            const msg = error.response?.data?.message || "Erro desconhecido";
            alert("Erro ao enviar: " + msg);
            setEnviandoPedido(false);
        });
    };

    // --- TELA (O que aparece no navegador) ---
    
    // Se não tiver token válido, mostra o Login
    if (!token || token === 'undefined') {
        return <Login onLoginSuccess={(t) => setToken(t)} />;
    }

    // Se tiver logado, mostra o App
    return (
        <div className="container" style={{ padding: '20px', fontFamily: 'sans-serif', paddingBottom: '100px' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', borderBottom: '2px solid #e3342f', paddingBottom: '10px' }}>
                <h1 style={{ color: '#e3342f', margin: 0 }}>🍗 Delivery</h1>
                <button onClick={logout} style={{ background: '#333', color: 'white', border: 'none', padding: '8px 15px', borderRadius: '4px', cursor: 'pointer' }}>Sair</button>
            </div>
            
            {loading ? <p style={{ textAlign: 'center' }}>Carregando...</p> : (
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '20px' }}>
                    {produtos.map(produto => (
                        <div key={produto.id} style={{ border: '1px solid #ddd', borderRadius: '8px', padding: '15px', textAlign: 'center', boxShadow: '0 4px 6px rgba(0,0,0,0.1)' }}>
                            <img src={produto.imagem || 'https://via.placeholder.com/150?text=Coxinha'} alt={produto.nome} style={{ width: '100%', height: '150px', objectFit: 'cover', borderRadius: '4px' }} />
                            <h3 style={{ margin: '10px 0', color: '#333' }}>{produto.nome}</h3>
                            <p style={{ color: '#666', fontSize: '14px' }}>{produto.descricao}</p>
                            <p style={{ fontWeight: 'bold', fontSize: '18px', color: '#e3342f' }}>R$ {parseFloat(produto.preco).toFixed(2).replace('.', ',')}</p>
                            <button onClick={() => adicionarAoCarrinho(produto)} style={{ background: '#e3342f', color: 'white', border: 'none', padding: '10px 20px', borderRadius: '4px', cursor: 'pointer', marginTop: '10px', width: '100%' }}>+ Adicionar</button>
                        </div>
                    ))}
                </div>
            )}

            {carrinho.length > 0 && (
                <div style={{ position: 'fixed', bottom: '0', left: '0', width: '100%', background: 'white', borderTop: '4px solid #e3342f', boxShadow: '0 -4px 10px rgba(0,0,0,0.2)', padding: '20px', boxSizing: 'border-box' }}>
                    <h3 style={{ margin: '0 0 10px 0' }}>🛒 Total: R$ {calcularTotal().toFixed(2)}</h3>
                    <div style={{ maxHeight: '100px', overflowY: 'auto', marginBottom: '10px' }}>
                        {carrinho.map(item => (
                            <div key={item.produto_id} style={{ display: 'flex', justifyContent: 'space-between', fontSize: '14px' }}>
                                <span>{item.quantidade}x {item.nome}</span>
                                <button onClick={() => removerDoCarrinho(item.produto_id)} style={{ color: 'red', border: 'none', background: 'none', cursor: 'pointer' }}>X</button>
                            </div>
                        ))}
                    </div>
                    <button onClick={finalizarPedido} disabled={enviandoPedido} style={{ background: '#28a745', color: 'white', border: 'none', padding: '15px 30px', borderRadius: '8px', cursor: 'pointer', width: '100%', fontSize: '18px', fontWeight: 'bold' }}>
                        {enviandoPedido ? 'Enviando...' : '✅ FINALIZAR PEDIDO'}
                    </button>
                </div>
            )}
        </div>
    );
}

if (document.getElementById('app')) {
    ReactDOM.createRoot(document.getElementById('app')).render(<React.StrictMode><App /></React.StrictMode>);
}
