import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/app/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        react(),
    ],
});
