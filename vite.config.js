import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig(({ command, mode }) => {
    const isDevelopment = mode === 'development';

    return {
        server: {
            // No seu PC (Sail), usa 0.0.0.0. No GitHub, usa o padrão.
            host: isDevelopment ? '0.0.0.0' : 'localhost',
            port: 5173,
            strictPort: true,
            hmr: {
                host: 'localhost',
            },
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.jsx'],
                refresh: true,
            }),
            react(),
        ],
    };
});
