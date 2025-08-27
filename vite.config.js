import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    define: {
        'import.meta.env.VITE_REVERB_APP_KEY': JSON.stringify(process.env.VITE_REVERB_APP_KEY),
        'import.meta.env.VITE_REVERB_HOST': JSON.stringify(process.env.VITE_REVERB_HOST),
        'import.meta.env.VITE_REVERB_PORT': JSON.stringify(process.env.VITE_REVERB_PORT),
        'import.meta.env.VITE_REVERB_SCHEME': JSON.stringify(process.env.VITE_REVERB_SCHEME),
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss', // Default app CSS
                'resources/js/app.js',   // Default app JS
                'resources/assets/custom/custom.js'
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
