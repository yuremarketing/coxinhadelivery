import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            // Aqui eu defino quais arquivos o Vite tem que vigiar.
            // Mudei de .js para .jsx porque agora decidi usar React na interface.
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        // Adicionei esse plugin aqui para ele traduzir o código React 
        // para algo que o navegador entenda.
        react(),
    ],
});
