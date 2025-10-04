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
    // Production configuration - WITH BASE PATH for Apache alias
    base: process.env.NODE_ENV === "production" ? "/super-admin/" : "/",
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
