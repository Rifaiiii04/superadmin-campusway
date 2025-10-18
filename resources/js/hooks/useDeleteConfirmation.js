import { useState, useCallback } from 'react';

const useDeleteConfirmation = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [deleteData, setDeleteData] = useState(null);
    const [isLoading, setIsLoading] = useState(false);

    const showDeleteConfirmation = useCallback((data) => {
        setDeleteData(data);
        setIsOpen(true);
    }, []);

    const hideDeleteConfirmation = useCallback(() => {
        if (!isLoading) {
            setIsOpen(false);
            setDeleteData(null);
        }
    }, [isLoading]);

    const confirmDelete = useCallback(async (onDelete) => {
        if (!deleteData || isLoading) return;

        setIsLoading(true);
        try {
            await onDelete(deleteData);
            hideDeleteConfirmation();
        } catch (error) {
            console.error('Delete error:', error);
            // Error handling is done in the component that uses this hook
        } finally {
            setIsLoading(false);
        }
    }, [deleteData, isLoading, hideDeleteConfirmation]);

    return {
        isOpen,
        deleteData,
        isLoading,
        showDeleteConfirmation,
        hideDeleteConfirmation,
        confirmDelete
    };
};

export default useDeleteConfirmation;
