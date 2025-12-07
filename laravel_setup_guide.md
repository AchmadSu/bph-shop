# Laravel Project Setup Guide

Ikuti langkah-langkah berikut untuk menjalankan project Laravel setelah clone/pull dari repository.

---

## **1. Clone / Pull Repository**
Jika belum clone repository:
```bash
git clone <repository-url>
```
Jika sudah clone dan ingin update:
```bash
git pull origin main
```

---

## **2. Install Dependencies**
Jalankan perintah berikut untuk menginstall semua package Laravel:
```bash
composer install
```
Jika ada package tambahan dibutuhkan:
```bash
composer require <package-name>
```

---

## **3. Generate Autoload**
```bash
composer dump-autoload
```

---

## **4. Setup Environment (.env)**
Copy file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Lalu sesuaikan konfigurasi database, app name, mail, dan lainnya sesuai kebutuhan.

---

## **5. Generate Application Key**
```bash
php artisan key:generate
```

---

## **6. Jalankan Migration & Seeder**
Tersedia 2 opsi:

### **Opsi A — Fresh Install:**
```bash
php artisan migrate --seed
```

### **Opsi B — Reset Semua Data:**
```bash
php artisan migrate:refresh --seed
```

---

## **7. Jalankan Server Local**
Laravel akan berjalan di `http://localhost:8000`:
```bash
php artisan serve
```

---

## **8. Test Cron / Schedule Command**
Untuk mengecek fitur auto cancel expired order:
```bash
php artisan orders:cancel-expired
```

---

## **Selesai 🎉**
Project Laravel berhasil dijalankan. Jika terjadi error atau butuh penjelasan tambahan, silakan cek dokumentasi Laravel atau hubungi tim pengembang.

