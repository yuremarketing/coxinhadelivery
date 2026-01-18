import React, { useState } from 'react';
import axios from 'axios';

export default function Login({ onLoginSuccess }) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        setError('');

        axios.post('/api/login', {
            email: email,
            password: password,
            device_name: 'ReactApp'
        })
        .then(response => {
            // A CORREÇÃO: Pegamos o access_token
            const token = response.data.access_token;
            
            localStorage.setItem('coxinha_token', token);
            
            if (onLoginSuccess) {
                onLoginSuccess(token);
            }
            window.location.reload();
        })
        .catch(err => {
            console.error(err);
            setError('E-mail ou senha incorretos!');
        });
    };

    return (
        <div style={{ maxWidth: '400px', margin: '50px auto', padding: '20px', border: '1px solid #ddd', borderRadius: '8px', textAlign: 'center', backgroundColor: '#fff' }}>
            <h2 style={{ color: '#e3342f' }}>🔐 Login Coxinha</h2>
            
            {error && <p style={{ color: 'white', background: '#e3342f', padding: '10px', borderRadius: '4px' }}>{error}</p>}

            <form onSubmit={handleSubmit}>
                <div style={{ marginBottom: '15px' }}>
                    <input 
                        type="email" 
                        placeholder="E-mail"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        style={{ width: '100%', padding: '10px', margin: '5px 0', border: '1px solid #ccc', borderRadius: '4px' }}
                        required
                    />
                </div>
                <div style={{ marginBottom: '15px' }}>
                    <input 
                        type="password" 
                        placeholder="Senha"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        style={{ width: '100%', padding: '10px', margin: '5px 0', border: '1px solid #ccc', borderRadius: '4px' }}
                        required
                    />
                </div>
                <button type="submit" style={{ background: '#e3342f', color: 'white', border: 'none', padding: '12px 20px', borderRadius: '4px', cursor: 'pointer', width: '100%', fontSize: '16px', fontWeight: 'bold' }}>
                    ENTRAR
                </button>
            </form>
        </div>
    );
}
