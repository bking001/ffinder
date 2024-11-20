import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import dns from 'dns'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {host: 'finder.airlink.ge'},
        host: 'finder.airlink.ge',
        port: 5173,
        https: {
            host: 'finder.airlink.ge',
            key: '/etc/ssl/airlink.key',
            cert: '/etc/ssl/airlink.crt',
            ciphers: 'TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256'
        }
    }
});

 
 

 