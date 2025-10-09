import React, { createContext, useContext, useState, useCallback } from 'react';
import Alert from '../Components/Alert';

const AlertContext = createContext();

export const useAlertContext = () => {
    const context = useContext(AlertContext);
    if (!context) {
        throw new Error('useAlertContext must be used within an AlertProvider');
    }
    return context;
};

export const AlertProvider = ({ children }) => {
    const [alerts, setAlerts] = useState([]);

    const addAlert = useCallback((message, type = 'success', options = {}) => {
        const id = Date.now() + Math.random();
        const newAlert = {
            id,
            message,
            type,
            duration: options.duration || 5000,
            position: options.position || 'top-right',
            show: true
        };
        
        setAlerts(prev => [...prev, newAlert]);
        
        return id;
    }, []);

    const removeAlert = useCallback((id) => {
        setAlerts(prev => prev.filter(alert => alert.id !== id));
    }, []);

    const showSuccess = useCallback((message, options = {}) => {
        return addAlert(message, 'success', options);
    }, [addAlert]);

    const showError = useCallback((message, options = {}) => {
        return addAlert(message, 'error', options);
    }, [addAlert]);

    const showWarning = useCallback((message, options = {}) => {
        return addAlert(message, 'warning', options);
    }, [addAlert]);

    const showInfo = useCallback((message, options = {}) => {
        return addAlert(message, 'info', options);
    }, [addAlert]);

    const clearAllAlerts = useCallback(() => {
        setAlerts([]);
    }, []);

    const value = {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        clearAllAlerts
    };

    return (
        <AlertContext.Provider value={value}>
            {children}
            {alerts.map((alert) => (
                <Alert
                    key={alert.id}
                    type={alert.type}
                    message={alert.message}
                    show={alert.show}
                    duration={alert.duration}
                    position={alert.position}
                    onClose={() => removeAlert(alert.id)}
                />
            ))}
        </AlertContext.Provider>
    );
};
