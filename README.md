# BPH SHOP Project Setup Guide

Ikuti langkah-langkah berikut untuk menjalankan project Laravel setelah clone/pull dari repository.

---

## **1. Clone / Pull Repository**
Jika belum clone repository:
```bash
git clone https://github.com/AchmadSu/bph-shop.git
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
Project BPH SHOP berhasil dijalankan. Jika terjadi error atau butuh penjelasan tambahan, silakan cek dokumentasi Laravel atau hubungi melalui:
📩 Email: ecepentis@gmail.com  
💬 WhatsApp: wa.me/6289658420438

## **DISCLAIMER 📌**
Pada soal test-project yang berbunyi:
"Barang yang ditambahkan ke keranjang pembeli tidak akan langsung mengurangi
stok yang tersedia. Stok hanya akan berkurang ketika pembayaran berhasil dilakukan dan
diverifikasi oleh Customer service Layer 1.
Jika pembeli tidak melakukan pembayaran atau pembayaran gagal dikonfirmasi oleh
Customer service Layer 1 dalam waktu maksimal 1x24 jam, pesanan akan otomatis
dibatalkan oleh sistem, dan barang akan kembali tersedia di stok."

Bagi saya pribadi terkesan ambigu, apakah stock berkurang ketika CS1 memverifikasi atau justru saat bukti pembayaran dikirim, karena ada kekhawatiran stock jadi bertambah dengan tidak sesuai, sehingga saya memutuskan flow nya adalah stock benar-benar berkurang ketika order diverifikasi oleh CS1. Sehingga untuk cancel order setelah 24 jam order tidak mendapatkan konfirmasi tidak akan mengembalikan stock hanya mengupdate status order saja. 

