import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/absensi-bulk.js',
                'resources/js/delete-handler.js',
                'resources/js/utils/cache.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        sourcemap: false,
        minify: 'terser',
        terserOptions: {
            compress: {
                // TEMPORARY: Keep console for debugging - Set to true for final production
                drop_console: false,
                drop_debugger: false
            }
        },
        rollupOptions: {
            output: {
                manualChunks: undefined,
                // Optimize for production
                entryFileNames: 'assets/[name].[hash].js',
                chunkFileNames: 'assets/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash][extname]'
            },
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['jquery', 'bootstrap']
    }
});