# Fix Apache Alias Configuration

## Masalah
Apache alias `/super-admin` mengarah ke folder yang salah:
- Saat ini: `/var/www/superadmin/superadmin-campusway/public`
- Seharusnya: `/var/www/superadmin/superadmin-backend/public`

## Solusi

### 1. Cek konfigurasi Apache yang aktif
```bash
sudo cat /etc/apache2/sites-enabled/103.23.198.101.conf
```

### 2. Edit konfigurasi Apache
```bash
sudo nano /etc/apache2/sites-enabled/103.23.198.101.conf
```

### 3. Pastikan alias mengarah ke folder yang benar
Cari baris:
```apache
Alias /super-admin /var/www/superadmin/superadmin-campusway/public
```

Ubah menjadi:
```apache
Alias /super-admin /var/www/superadmin/superadmin-backend/public
```

### 4. Pastikan Directory directive juga benar
Cari:
```apache
<Directory /var/www/superadmin/superadmin-campusway/public>
```

Ubah menjadi:
```apache
<Directory /var/www/superadmin/superadmin-backend/public>
```

### 5. Restart Apache
```bash
sudo systemctl restart apache2
```

### 6. Test lagi
```bash
curl http://103.23.198.101/super-admin/api/school/test-student-detail/40
```

## Alternatif: Cek apakah ada multiple alias

Jika ada multiple alias, pastikan hanya satu yang aktif dan mengarah ke folder yang benar.

