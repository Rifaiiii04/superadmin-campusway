import { useState, useCallback } from 'react';

const useAlert = () => {
    const [alert, setAlert] = useState({
        show: false,
        type: 'success',
        message: '',
        duration: 5000,
        position: 'top-right'
    });

    const showAlert = useCallback((message, type = 'success', options = {}) => {
        setAlert({
            show: true,
            message,
            type,
            duration: options.duration || 5000,
            position: options.position || 'top-right'
        });
    }, []);

    const showSuccess = useCallback((message, options = {}) => {
        showAlert(message, 'success', options);
    }, [showAlert]);

    const showError = useCallback((message, options = {}) => {
        showAlert(message, 'error', options);
    }, [showAlert]);

    const showWarning = useCallback((message, options = {}) => {
        showAlert(message, 'warning', options);
    }, [showAlert]);

    const showInfo = useCallback((message, options = {}) => {
        showAlert(message, 'info', options);
    }, [showAlert]);

    const hideAlert = useCallback(() => {
        setAlert(prev => ({ ...prev, show: false }));
    }, []);

    return {
        alert,
        showAlert,
        showSuccess,
        showError,
        showWarning,
        showInfo,
        hideAlert
    };
};

export default useAlert;
