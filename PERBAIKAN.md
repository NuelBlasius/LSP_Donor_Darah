# DonorDarah - catatan perbaikan

## Perubahan yang diterapkan

- Verifikasi akun pendonor memiliki filter nama dan tanggal registrasi, serta modal detail profil. NIK dan nomor telepon disamarkan dengan tombol reveal.
- Verifikasi pendaftaran dan pembayaran berada di menu **Pendaftaran** admin. Klik baris untuk membuka detail pendaftaran, data pendonor, nominal, dan bukti bayar.
- Menu sidebar **Pembayaran** terpisah dihapus. Route lama `admin.payments.index` diarahkan ke halaman Pendaftaran agar tautan lama tidak menyebabkan RouteNotFoundException.
- Admin dapat menyetujui/menolak pendaftaran dan memverifikasi/menolak bukti pembayaran dari modal.
- Nominal dapat diatur saat membuat/mengedit jadwal, otomatis diformat dengan pemisah titik, dan ditampilkan pada jadwal serta pembayaran.
- Pembayaran demo memiliki pilihan Virtual Account dan QRIS, kode berlaku 15 menit, serta upload bukti pada halaman pembayaran.
- Foto bukti dioptimalkan di browser (resize maksimal 1400 px dan WebP bila didukung). Server menerima JPG, PNG, WebP maksimal 2 MB.
- Setelah pembayaran disetujui, notifikasi konfirmasi muncul pada dashboard pendonor.

## Menjalankan setelah mengganti kode

Di folder project:

```powershell
composer install
npm install
php artisan migrate
php artisan storage:link
npm run build
php artisan route:clear
php artisan view:clear
php artisan serve
```

Jangan menjalankan `migrate:fresh` kecuali memang ingin menghapus seluruh data database.

## Catatan demo

Nomor Virtual Account dan gambar QRIS adalah **dummy**, bukan sarana pembayaran nyata. Gambar QRIS tidak dapat dipindai untuk transaksi sungguhan. Admin tetap memeriksa bukti pembayaran yang diunggah.

Bukti pembayaran disimpan di disk `public` dalam folder `payment-proofs`. Jalankan `php artisan storage:link` agar gambar bukti dapat ditampilkan oleh admin.

## File yang tidak lagi digunakan

- `resources/views/admin/payments/index.blade.php` adalah view pembayaran terpisah yang tidak lagi digunakan karena pembayaran telah digabung ke halaman Pendaftaran. View ini dihapus dari paket perbaikan.
