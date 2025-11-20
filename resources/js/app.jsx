import "./bootstrap";
import "../css/app.css";
import "../css/superadmin.css";

import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import QueryProvider from "./Providers/QueryProvider";
import { AlertProvider } from "./Providers/AlertProvider";
import axios from 'axios';

// Configure axios for Inertia
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob("./Pages/**/*.jsx")
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        // Security: Clear any sensitive data from localStorage/sessionStorage on app init
        // This ensures no leftover data from previous sessions
        if (typeof window !== 'undefined') {
            // Only clear if not authenticated (on login page)
            const isLoginPage = window.location.pathname.includes('/login');
            if (isLoginPage) {
                // Clear any potentially sensitive data
                const sensitiveKeys = ['token', 'password', 'credential', 'secret', 'key'];
                sensitiveKeys.forEach(key => {
                    Object.keys(localStorage).forEach(localKey => {
                        if (localKey.toLowerCase().includes(key)) {
                            localStorage.removeItem(localKey);
                        }
                    });
                    Object.keys(sessionStorage).forEach(sessionKey => {
                        if (sessionKey.toLowerCase().includes(key)) {
                            sessionStorage.removeItem(sessionKey);
                        }
                    });
                });
            }
        }

        root.render(
            <AlertProvider>
                <QueryProvider>
                    <App {...props} />
                </QueryProvider>
            </AlertProvider>
        );
    },
    progress: {
        color: "#4B5563",
    },
});
