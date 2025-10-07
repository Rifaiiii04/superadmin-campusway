// Super Admin Campusway - Pure JavaScript (NO IMPORTS/EXPORTS)
console.log('🚀 Starting Super Admin Application...');

// Simple React application without any imports
function startApplication() {
    console.log('🔧 Checking dependencies...');
    
    // Check if React is available
    if (typeof window.React === 'undefined') {
        console.error('❌ React not found in window object');
        showErrorMessage('React library not loaded properly');
        return;
    }
    
    if (typeof window.ReactDOM === 'undefined') {
        console.error('❌ ReactDOM not found in window object');
        showErrorMessage('ReactDOM library not loaded properly');
        return;
    }
    
    console.log('✅ All dependencies loaded');
    
    // Use React from global window object
    var React = window.React;
    var ReactDOM = window.ReactDOM;
    
    // Create login form component using React.createElement (no JSX)
    function LoginForm() {
        var state = React.useState('');
        var username = state[0];
        var setUsername = state[1];
        
        var state2 = React.useState('');
        var password = state2[0];
        var setPassword = state2[1];
        
        var state3 = React.useState(false);
        var loading = state3[0];
        var setLoading = state3[1];
        
        var state4 = React.useState('');
        var error = state4[0];
        var setError = state4[1];
        
        function handleSubmit(e) {
            e.preventDefault();
            setError('');
            
            if (!username) {
                setError('Please enter username');
                return;
            }
            
            if (!password) {
                setError('Please enter password');
                return;
            }
            
            setLoading(true);
            console.log('Login attempt for user:', username);
            
            // Simulate API call
            setTimeout(function() {
                setLoading(false);
                
                // Demo authentication
                if (username === 'admin' && password === 'admin') {
                    alert('Login successful! Welcome to Super Admin Dashboard.');
                    // In real app: window.location.href = '/dashboard';
                } else {
                    setError('Invalid credentials. Use admin/admin');
                }
            }, 1000);
        }
        
        // Create UI using React.createElement
        return React.createElement('div', {
            style: {
                minHeight: '100vh',
                background: 'linear-gradient(135deg, #800000 0%, #a00000 100%)',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                padding: '20px'
            }
        }, 
        React.createElement('div', {
            style: {
                background: 'white',
                padding: '40px',
                borderRadius: '12px',
                boxShadow: '0 10px 30px rgba(0,0,0,0.3)',
                width: '100%',
                maxWidth: '400px'
            }
        }, [
            // Header
            React.createElement('div', {
                key: 'header',
                style: {
                    textAlign: 'center',
                    marginBottom: '30px'
                }
            }, [
                React.createElement('h1', {
                    key: 'title',
                    style: {
                        color: '#800000',
                        margin: '0 0 10px 0',
                        fontSize: '28px'
                    }
                }, '🎓 Super Admin'),
                React.createElement('p', {
                    key: 'subtitle',
                    style: {
                        color: '#666',
                        margin: 0
                    }
                }, 'Campusway Management System')
            ]),
            
            // Error message
            error && React.createElement('div', {
                key: 'error',
                style: {
                    background: '#fee',
                    border: '1px solid #fcc',
                    borderRadius: '6px',
                    padding: '12px',
                    marginBottom: '20px',
                    color: '#c00',
                    fontSize: '14px'
                }
            }, error),
            
            // Login form
            React.createElement('form', {
                key: 'form',
                onSubmit: handleSubmit
            }, [
                // Username field
                React.createElement('div', {
                    key: 'username-field',
                    style: {
                        marginBottom: '20px'
                    }
                }, [
                    React.createElement('label', {
                        key: 'label',
                        htmlFor: 'username',
                        style: {
                            display: 'block',
                            marginBottom: '8px',
                            fontWeight: 'bold',
                            color: '#333'
                        }
                    }, 'Username'),
                    React.createElement('input', {
                        key: 'input',
                        type: 'text',
                        id: 'username',
                        value: username,
                        onChange: function(e) {
                            setUsername(e.target.value);
                            setError('');
                        },
                        placeholder: 'Enter username',
                        disabled: loading,
                        style: {
                            width: '100%',
                            padding: '12px',
                            border: '1px solid #ddd',
                            borderRadius: '6px',
                            fontSize: '16px',
                            boxSizing: 'border-box'
                        }
                    })
                ]),
                
                // Password field
                React.createElement('div', {
                    key: 'password-field',
                    style: {
                        marginBottom: '25px'
                    }
                }, [
                    React.createElement('label', {
                        key: 'label',
                        htmlFor: 'password',
                        style: {
                            display: 'block',
                            marginBottom: '8px',
                            fontWeight: 'bold',
                            color: '#333'
                        }
                    }, 'Password'),
                    React.createElement('input', {
                        key: 'input',
                        type: 'password',
                        id: 'password',
                        value: password,
                        onChange: function(e) {
                            setPassword(e.target.value);
                            setError('');
                        },
                        placeholder: 'Enter password',
                        disabled: loading,
                        style: {
                            width: '100%',
                            padding: '12px',
                            border: '1px solid #ddd',
                            borderRadius: '6px',
                            fontSize: '16px',
                            boxSizing: 'border-box'
                        }
                    })
                ]),
                
                // Submit button
                React.createElement('button', {
                    key: 'submit',
                    type: 'submit',
                    disabled: loading,
                    style: {
                        width: '100%',
                        padding: '14px',
                        background: loading ? '#ccc' : '#800000',
                        color: 'white',
                        border: 'none',
                        borderRadius: '6px',
                        fontSize: '16px',
                        fontWeight: 'bold',
                        cursor: loading ? 'not-allowed' : 'pointer'
                    }
                }, loading ? 'Logging in...' : 'Login')
            ]),
            
            // Demo info
            React.createElement('div', {
                key: 'demo-info',
                style: {
                    marginTop: '25px',
                    padding: '15px',
                    background: '#f8f9fa',
                    borderRadius: '6px',
                    fontSize: '14px',
                    color: '#666'
                }
            }, [
                React.createElement('div', {
                    key: 'title',
                    style: {
                        fontWeight: 'bold',
                        marginBottom: '5px'
                    }
                }, 'Demo Credentials:'),
                React.createElement('div', {
                    key: 'credentials'
                }, 'Username: admin, Password: admin')
            ]),
            
            // Status
            React.createElement('div', {
                key: 'status',
                style: {
                    marginTop: '20px',
                    padding: '15px',
                    background: '#f0f9f0',
                    border: '1px solid #28a745',
                    borderRadius: '6px',
                    textAlign: 'center'
                }
            }, [
                React.createElement('div', {
                    key: 'text',
                    style: {
                        color: '#28a745',
                        fontWeight: 'bold'
                    }
                }, '✅ React Application Ready')
            ])
        ]));
    }
    
    // Render the application
    var appElement = document.getElementById('app');
    if (appElement) {
        try {
            var root = ReactDOM.createRoot(appElement);
            root.render(React.createElement(LoginForm));
            console.log('✅ Super Admin application rendered successfully!');
        } catch (err) {
            console.error('❌ Rendering error:', err);
            showErrorMessage('Failed to render: ' + err.message);
        }
    } else {
        console.error('❌ App container not found');
        showErrorMessage('Application container missing');
    }
}

// Error display function
function showErrorMessage(message) {
    var appElement = document.getElementById('app');
    if (appElement) {
        appElement.innerHTML = '\
            <div style="min-height: 100vh; background: #800000; display: flex; justify-content: center; align-items: center; color: white; padding: 20px;">\
                <div style="background: white; color: #c00; padding: 30px; border-radius: 10px; text-align: center; max-width: 500px;">\
                    <h2 style="margin: 0 0 15px 0;">⚠️ Application Error</h2>\
                    <p style="margin: 0 0 20px 0;">' + message + '</p>\
                    <button onclick="location.reload()" style="padding: 10px 20px; background: #800000; color: white; border: none; border-radius: 5px; cursor: pointer;">\
                        Reload Page\
                    </button>\
                </div>\
            </div>\
        ';
    }
}

// Wait for everything to be ready
function init() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
        return;
    }
    
    if (!window.React || !window.ReactDOM) {
        setTimeout(init, 100);
        return;
    }
    
    console.log('🎉 Starting application initialization...');
    startApplication();
}

// Start the app
init();
console.log('📦 Application script loaded');
