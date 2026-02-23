import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js', 'resources/css/theme.css', 'resources/js/theme-switcher.js'],
            refresh: true,
        }),
    ],
});
