# Alert System Documentation

## Overview
Sistem alert modern yang menggantikan `alert()` browser dengan popup yang lebih user-friendly dan customizable.

## Components

### 1. Alert Component
Komponen dasar untuk menampilkan alert popup.

**Props:**
- `type`: 'success' | 'error' | 'warning' | 'info' (default: 'success')
- `message`: string - pesan yang akan ditampilkan
- `show`: boolean - apakah alert ditampilkan
- `onClose`: function - callback ketika alert ditutup
- `duration`: number - durasi auto-dismiss dalam ms (default: 5000)
- `position`: string - posisi alert (default: 'top-right')

**Posisi yang tersedia:**
- 'top-left', 'top-center', 'top-right'
- 'bottom-left', 'bottom-center', 'bottom-right'

### 2. useAlert Hook
Hook untuk mengelola alert dalam komponen.

```javascript
import { useAlertContext } from '@/Providers/AlertProvider';

const { showSuccess, showError, showWarning, showInfo } = useAlertContext();
```

**Methods:**
- `showSuccess(message, options)`
- `showError(message, options)`
- `showWarning(message, options)`
- `showInfo(message, options)`

### 3. AlertProvider
Provider untuk mengelola state alert global.

## Usage Examples

### Basic Usage
```javascript
import { useAlertContext } from '@/Providers/AlertProvider';

function MyComponent() {
    const { showSuccess, showError } = useAlertContext();

    const handleSubmit = () => {
        try {
            // Process data
            showSuccess('Data berhasil disimpan!');
        } catch (error) {
            showError('Terjadi kesalahan: ' + error.message);
        }
    };

    return <button onClick={handleSubmit}>Submit</button>;
}
```

### Advanced Usage
```javascript
const { showSuccess, showError, showWarning, showInfo } = useAlertContext();

// Success dengan custom duration dan position
showSuccess('Operasi berhasil!', {
    duration: 3000,
    position: 'top-center'
});

// Error dengan custom duration
showError('Terjadi kesalahan!', {
    duration: 8000,
    position: 'bottom-right'
});

// Warning
showWarning('Perhatian: Data akan dihapus!', {
    duration: 5000
});

// Info
showInfo('Sistem akan maintenance dalam 5 menit', {
    duration: 10000
});
```

### Multiple Alerts
```javascript
const handleMultipleAlerts = () => {
    showSuccess('Data tersimpan!');
    setTimeout(() => showInfo('Mengirim notifikasi...'), 1000);
    setTimeout(() => showWarning('Periksa kembali data!'), 2000);
};
```

## Styling

Alert menggunakan Tailwind CSS dengan warna yang berbeda untuk setiap tipe:

- **Success**: Green (bg-green-50, border-green-200, text-green-800)
- **Error**: Red (bg-red-50, border-red-200, text-red-800)
- **Warning**: Yellow (bg-yellow-50, border-yellow-200, text-yellow-800)
- **Info**: Blue (bg-blue-50, border-blue-200, text-blue-800)

## Features

1. **Auto-dismiss**: Alert otomatis hilang setelah durasi tertentu
2. **Manual close**: User bisa menutup alert dengan tombol X
3. **Smooth animations**: Transisi masuk dan keluar yang halus
4. **Multiple positions**: 6 posisi berbeda untuk menampilkan alert
5. **Multiple alerts**: Bisa menampilkan beberapa alert sekaligus
6. **Customizable duration**: Durasi yang bisa disesuaikan per alert
7. **Responsive**: Tampilan yang responsif di berbagai ukuran layar

## Migration from alert()

Ganti semua penggunaan `alert()` dengan alert system yang baru:

```javascript
// Before
alert('Data berhasil disimpan!');

// After
showSuccess('Data berhasil disimpan!');
```

```javascript
// Before
alert('Error: ' + error.message);

// After
showError('Error: ' + error.message);
```

## Best Practices

1. **Gunakan tipe yang sesuai**: Success untuk operasi berhasil, Error untuk kesalahan, Warning untuk peringatan, Info untuk informasi
2. **Pesan yang jelas**: Gunakan pesan yang mudah dipahami user
3. **Durasi yang tepat**: Success bisa lebih cepat (3-5 detik), Error bisa lebih lama (5-8 detik)
4. **Posisi yang konsisten**: Gunakan posisi yang sama untuk tipe alert yang sama
5. **Jangan terlalu banyak**: Hindari menampilkan terlalu banyak alert sekaligus

## Troubleshooting

### Alert tidak muncul
- Pastikan komponen dibungkus dengan `AlertProvider`
- Pastikan menggunakan `useAlertContext()` dengan benar
- Cek console untuk error

### Alert tidak hilang
- Pastikan `duration` tidak 0
- Cek apakah ada error dalam `onClose` callback

### Styling tidak sesuai
- Pastikan Tailwind CSS sudah ter-load
- Cek apakah ada CSS yang override styling alert
