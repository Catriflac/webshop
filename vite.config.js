import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })],
    server: {
        host: '0.0.0.0', // Allow access from any device on the network
        port: 5173,
        strictPort: true,
        hmr: {
            host: '192.168.0.249' // Replace with your actual local IP
        }
    }
});
