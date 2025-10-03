import React from "react";
import { Trash2, AlertTriangle } from "lucide-react";

export default function DeleteConfirmationModal({
    isOpen,
    onClose,
    onConfirm,
    questionTitle = "soal ini",
    questionId,
}) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div className="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 max-w-sm sm:w-96 shadow-lg rounded-md bg-white">
                <div className="mt-3 text-center">
                    <div className="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100">
                        <AlertTriangle className="h-5 w-5 sm:h-6 sm:w-6 text-red-600" />
                    </div>
                    <h3 className="text-base sm:text-lg font-medium text-gray-900 mt-4">
                        Konfirmasi Hapus
                    </h3>
                    <div className="mt-2 px-4 sm:px-7 py-3">
                        <p className="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus {questionTitle}?
                        </p>
                        <p className="text-xs text-gray-400 mt-2">
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div className="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3 mt-4">
                        <button
                            onClick={onClose}
                            className="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                            Batal
                        </button>
                        <button
                            onClick={() => {
                                onConfirm(questionId);
                                onClose();
                            }}
                            className="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            <Trash2 className="h-4 w-4 inline mr-2" />
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
