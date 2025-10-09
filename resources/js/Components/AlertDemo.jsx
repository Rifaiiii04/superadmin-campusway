import React from 'react';
import { useAlertContext } from '@/Providers/AlertProvider';

const AlertDemo = () => {
    const { showSuccess, showError, showWarning, showInfo } = useAlertContext();

    const handleSuccess = () => {
        showSuccess('Operasi berhasil dilakukan!', {
            duration: 3000,
            position: 'top-right'
        });
    };

    const handleError = () => {
        showError('Terjadi kesalahan saat memproses data!', {
            duration: 5000,
            position: 'top-center'
        });
    };

    const handleWarning = () => {
        showWarning('Perhatian: Data akan dihapus permanen!', {
            duration: 4000,
            position: 'bottom-right'
        });
    };

    const handleInfo = () => {
        showInfo('Informasi: Sistem akan maintenance dalam 5 menit', {
            duration: 6000,
            position: 'bottom-left'
        });
    };

    const handleMultiple = () => {
        showSuccess('Data tersimpan!');
        setTimeout(() => showInfo('Mengirim notifikasi...'), 1000);
        setTimeout(() => showWarning('Periksa kembali data!'), 2000);
    };

    return (
        <div className="p-6 bg-white rounded-lg shadow-lg">
            <h2 className="text-2xl font-bold mb-4 text-gray-800">Demo Alert System</h2>
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                <button
                    onClick={handleSuccess}
                    className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
                >
                    Success Alert
                </button>
                <button
                    onClick={handleError}
                    className="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                >
                    Error Alert
                </button>
                <button
                    onClick={handleWarning}
                    className="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors"
                >
                    Warning Alert
                </button>
                <button
                    onClick={handleInfo}
                    className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                >
                    Info Alert
                </button>
                <button
                    onClick={handleMultiple}
                    className="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors col-span-2"
                >
                    Multiple Alerts
                </button>
            </div>
        </div>
    );
};

export default AlertDemo;
