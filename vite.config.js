import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    build: {
        // Generate manifest.json in outDir
        manifest: true,
        rollupOptions: {
            // Overwrite default .html entry
            input: './src/main.jsx',
        },
    },
    server: {
        // Origin for the dev server
        origin: 'http://localhost:5173',
    },
});
