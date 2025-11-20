    import React from "react";
    import { Head, useForm } from "@inertiajs/react";
    import { Building2, Eye, EyeOff } from "lucide-react";

    export default function SuperAdminLogin() {
        const [showPassword, setShowPassword] = React.useState(false);
        const { data, setData, post, processing, errors } = useForm({
            username: "",
            password: "",
        });

        // Security: Clear any sensitive data from storage on component mount
        React.useEffect(() => {
            if (typeof window !== 'undefined') {
                // Clear any potentially sensitive data from previous sessions
                const sensitiveKeys = ['token', 'password', 'credential', 'secret', 'key', 'auth'];
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
        }, []);

        const handleSubmit = (e) => {
            e.preventDefault();
            
            // Try to refresh CSRF token before submitting if available
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                // Token exists, proceed with login
            } else {
                // No CSRF token found, try to get it
                fetch('/csrf-token')
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            // Update meta tag
                            const meta = document.createElement('meta');
                            meta.name = 'csrf-token';
                            meta.content = data.csrf_token;
                            document.head.appendChild(meta);
                        }
                    })
                    .catch(() => {
                        // If can't get token, just proceed - Inertia will handle it
                    });
            }
            
            post("/login", {
                onSuccess: (page) => {
                    // Login successful - no console log needed
                    // Inertia akan handle redirect otomatis
                },
                onError: (errors) => {
                    // Determine if this is a validation/credential error (should not be logged)
                    const isValidationError = errors.username || errors.password;
                    const errorMessage = errors.message || errors.error || JSON.stringify(errors);
                    
                    // Handle 419 CSRF token mismatch error
                    const has419Error = errorMessage && (
                        errorMessage.includes('419') || 
                        errorMessage.includes('CSRF') ||
                        errorMessage.includes('token mismatch') ||
                        errorMessage.includes('Session telah berakhir') ||
                        errorMessage.includes('The page has expired') ||
                        errorMessage.includes('PAGE EXPIRED')
                    );
                    
                    if (has419Error) {
                        // Silently reload page to get fresh CSRF token (no alert, no console error)
                        // The page reload will get a fresh token automatically
                        window.location.reload();
                        return;
                    }
                    
                    // Only log non-validation errors to console
                    if (!isValidationError && errors.message) {
                        // Only log if it's not a credential/validation error
                        const message = String(errors.message).toLowerCase();
                        if (!message.includes('password') && 
                            !message.includes('username') && 
                            !message.includes('tidak ditemukan') &&
                            !message.includes('salah')) {
                            console.error("Login error:", errors.message);
                        }
                    }
                },
                onFinish: () => {
                    // Process finished - no console log needed
                },
                preserveState: false,
                preserveScroll: false,
            });
        };

        return (
            <>
                <Head title="Super Admin Login" />

                <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
                    <div className="sm:mx-auto sm:w-full sm:max-w-md">
                        <div className="flex justify-center">
                            <Building2 className="h-12 w-12 sm:h-16 sm:w-16 text-maroon-600" />
                        </div>
                        <h2 className="mt-4 sm:mt-6 text-center text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">
                            Super Admin Login
                        </h2>
                        <p className="mt-2 text-center text-sm text-gray-600">
                            Masuk ke dashboard Super Admin
                        </p>
                    </div>

                    <div className="mt-6 sm:mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                        <div className="bg-white py-6 sm:py-8 px-4  shadow sm:rounded-lg sm:px-10">
                            <form
                                className="space-y-4 sm:space-y-6"
                                onSubmit={handleSubmit}
                            >
                                <div>
                                    <label
                                        htmlFor="username"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Username
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="username"
                                            name="username"
                                            type="text"
                                            autoComplete="username"
                                            required
                                            value={data.username}
                                            onChange={(e) =>
                                                setData("username", e.target.value)
                                            }
                                            className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500 text-sm"
                                            placeholder="Masukkan username"
                                        />
                                    </div>
                                    {errors.username && (
                                        <div className="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                                            <p className="text-sm text-red-800 font-medium">
                                                {errors.username}
                                            </p>
                                        </div>
                                    )}
                                </div>

                                <div>
                                    <label
                                        htmlFor="password"
                                        className="block text-sm font-medium text-gray-700"
                                    >
                                        Password
                                    </label>
                                    <div className="mt-1 relative">
                                        <input
                                            id="password"
                                            name="password"
                                            type={
                                                showPassword ? "text" : "password"
                                            }
                                            autoComplete="current-password"
                                            required
                                            value={data.password}
                                            onChange={(e) =>
                                                setData("password", e.target.value)
                                            }
                                            className="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500 text-sm pr-10"
                                            placeholder="Masukkan password"
                                        />
                                        <button
                                            type="button"
                                            className="absolute inset-y-0 right-0 pr-3 flex items-center"
                                            onClick={() =>
                                                setShowPassword(!showPassword)
                                            }
                                        >
                                            {showPassword ? (
                                                <EyeOff className="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" />
                                            ) : (
                                                <Eye className="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" />
                                            )}
                                        </button>
                                    </div>
                                    {errors.password && (
                                        <div className="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                                            <p className="text-sm text-red-800 font-medium">
                                                {errors.password}
                                            </p>
                                        </div>
                                    )}
                                    {errors.message && (
                                        <div className="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                            <p className="text-sm text-yellow-800 font-medium">
                                                {errors.message}
                                            </p>
                                        </div>
                                    )}
                                </div>

                                <div>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 disabled:opacity-50"
                                    >
                                        {processing ? "Memproses..." : "Masuk"}
                                    </button>
                                </div>
                            </form>

                            <div className="mt-6">
                                <div className="relative">
                                    <div className="absolute inset-0 flex items-center">
                                        <div className="w-full border-t border-gray-300" />
                                    </div>
                                    <div className="relative flex justify-center text-sm">
                                        <span className="px-2 bg-white text-gray-500">
                                            Sistem Pendidikan Nasional
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </>
        );
    }
