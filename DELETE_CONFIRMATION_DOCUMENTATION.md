# Delete Confirmation System Documentation

## Overview
Sistem konfirmasi DELETE yang modern dan user-friendly untuk menggantikan `confirm()` browser dengan modal yang lebih menarik dan informatif.

## Components

### 1. DeleteConfirmationModal Component
Komponen modal untuk konfirmasi DELETE dengan desain yang modern.

**Props:**
- `isOpen`: boolean - apakah modal ditampilkan
- `onClose`: function - callback ketika modal ditutup
- `onConfirm`: function - callback ketika konfirmasi DELETE
- `title`: string - judul modal (default: "Konfirmasi Hapus")
- `message`: string - pesan konfirmasi
- `itemName`: string - nama item yang akan dihapus
- `isLoading`: boolean - apakah sedang dalam proses DELETE
- `confirmText`: string - teks tombol konfirmasi (default: "Ya, Hapus")
- `cancelText`: string - teks tombol batal (default: "Batal")

**Features:**
- ✅ **Modern Design**: Menggunakan Tailwind CSS dengan desain yang clean
- ✅ **Loading State**: Menampilkan loading spinner saat proses DELETE
- ✅ **Item Name Display**: Menampilkan nama item yang akan dihapus
- ✅ **Responsive**: Tampilan yang responsif di berbagai ukuran layar
- ✅ **Accessibility**: Support keyboard navigation dan screen reader
- ✅ **Smooth Animations**: Transisi masuk dan keluar yang halus

### 2. useDeleteConfirmation Hook
Hook untuk mengelola state konfirmasi DELETE.

**Methods:**
- `showDeleteConfirmation(data)`: Menampilkan modal konfirmasi
- `hideDeleteConfirmation()`: Menyembunyikan modal konfirmasi
- `confirmDelete(onDelete)`: Menjalankan proses DELETE

**State:**
- `isOpen`: boolean - apakah modal ditampilkan
- `deleteData`: object - data item yang akan dihapus
- `isLoading`: boolean - apakah sedang dalam proses DELETE

## Usage Examples

### Basic Usage
```javascript
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';

function MyComponent() {
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deletingItem, setDeletingItem] = useState(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDeleteClick = (item) => {
        setDeletingItem(item);
        setShowDeleteModal(true);
    };

    const handleConfirmDelete = async () => {
        if (!deletingItem) return;
        
        setIsDeleting(true);
        try {
            // Perform delete operation
            await deleteItem(deletingItem.id);
            setShowDeleteModal(false);
            setDeletingItem(null);
        } catch (error) {
            console.error('Delete error:', error);
        } finally {
            setIsDeleting(false);
        }
    };

    const handleCancelDelete = () => {
        if (!isDeleting) {
            setShowDeleteModal(false);
            setDeletingItem(null);
        }
    };

    return (
        <div>
            {/* Your component content */}
            
            <DeleteConfirmationModal
                isOpen={showDeleteModal}
                onClose={handleCancelDelete}
                onConfirm={handleConfirmDelete}
                title="Konfirmasi Hapus Item"
                message="Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan."
                itemName={deletingItem?.name}
                isLoading={isDeleting}
                confirmText="Ya, Hapus Item"
                cancelText="Batal"
            />
        </div>
    );
}
```

### Using Hook
```javascript
import { useDeleteConfirmation } from '@/hooks/useDeleteConfirmation';

function MyComponent() {
    const { 
        isOpen, 
        deleteData, 
        isLoading, 
        showDeleteConfirmation, 
        hideDeleteConfirmation, 
        confirmDelete 
    } = useDeleteConfirmation();

    const handleDeleteClick = (item) => {
        showDeleteConfirmation({
            id: item.id,
            name: item.name,
            type: 'item'
        });
    };

    const handleConfirmDelete = async (data) => {
        await confirmDelete(async (deleteData) => {
            await deleteItem(deleteData.id);
        });
    };

    return (
        <div>
            {/* Your component content */}
            
            <DeleteConfirmationModal
                isOpen={isOpen}
                onClose={hideDeleteConfirmation}
                onConfirm={handleConfirmDelete}
                title="Konfirmasi Hapus Item"
                message="Apakah Anda yakin ingin menghapus item ini?"
                itemName={deleteData?.name}
                isLoading={isLoading}
            />
        </div>
    );
}
```

## Implemented in Components

### 1. Students.jsx
- **Title**: "Konfirmasi Hapus Siswa"
- **Message**: "Apakah Anda yakin ingin menghapus siswa ini? Tindakan ini tidak dapat dibatalkan."
- **Item Name**: Student name
- **Confirm Text**: "Ya, Hapus Siswa"

### 2. MajorRecommendations.jsx
- **Title**: "Konfirmasi Hapus Jurusan"
- **Message**: "Apakah Anda yakin ingin menghapus jurusan ini? Tindakan ini tidak dapat dibatalkan."
- **Item Name**: Major name
- **Confirm Text**: "Ya, Hapus Jurusan"

## Styling

Modal menggunakan Tailwind CSS dengan warna yang konsisten:

- **Background**: White dengan shadow
- **Header Icon**: Red background dengan warning icon
- **Confirm Button**: Red background (bg-red-600)
- **Cancel Button**: White background dengan gray border
- **Loading State**: Spinner animation dengan disabled state

## Features

1. **Confirmation Required**: User harus mengkonfirmasi sebelum DELETE
2. **Item Name Display**: Menampilkan nama item yang akan dihapus
3. **Loading State**: Menampilkan loading saat proses DELETE
4. **Error Handling**: Menangani error dengan proper feedback
5. **Responsive Design**: Tampilan yang bagus di semua ukuran layar
6. **Accessibility**: Support keyboard navigation dan screen reader
7. **Smooth Animations**: Transisi yang halus dan professional

## Migration from confirm()

Ganti semua penggunaan `confirm()` dengan sistem konfirmasi modal:

```javascript
// Before
const handleDelete = (id) => {
    if (confirm('Are you sure?')) {
        deleteItem(id);
    }
};

// After
const handleDeleteClick = (item) => {
    setDeletingItem(item);
    setShowDeleteModal(true);
};

const handleConfirmDelete = async () => {
    if (!deletingItem) return;
    
    setIsDeleting(true);
    try {
        await deleteItem(deletingItem.id);
        setShowDeleteModal(false);
        setDeletingItem(null);
    } catch (error) {
        console.error('Delete error:', error);
    } finally {
        setIsDeleting(false);
    }
};
```

## Best Practices

1. **Always Show Confirmation**: Jangan biarkan user menghapus tanpa konfirmasi
2. **Show Item Name**: Tampilkan nama item yang akan dihapus
3. **Loading State**: Tampilkan loading saat proses DELETE
4. **Error Handling**: Tangani error dengan proper feedback
5. **Consistent Styling**: Gunakan styling yang konsisten di seluruh aplikasi
6. **Accessibility**: Pastikan modal accessible untuk semua user

## Troubleshooting

### Modal tidak muncul
- Pastikan `isOpen` state sudah di-set ke `true`
- Cek apakah ada error di console
- Pastikan komponen sudah di-import dengan benar

### DELETE tidak berjalan
- Pastikan `onConfirm` function sudah di-implementasi
- Cek apakah ada error di console
- Pastikan CSRF token sudah dikirim dengan benar

### Styling tidak sesuai
- Pastikan Tailwind CSS sudah ter-load
- Cek apakah ada CSS yang override styling modal
- Pastikan semua class Tailwind tersedia
