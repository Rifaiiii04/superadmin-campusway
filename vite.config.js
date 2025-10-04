import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.jsx",
            refresh: true,
        }),
        react(),
    ],
    // Production configuration - NO BASE PATH (Apache handles alias)
    base: "/",
    build: {
        outDir: "public/build",
        assetsDir: "assets",
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["react", "react-dom"],
                    inertia: ["@inertiajs/react"],
                    query: ["@tanstack/react-query"],
                },
            },
        },
    },
    server: {
        hmr: {
            host: "localhost",
        },
    },
});
