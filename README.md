# DonorDarah - Sistem Pendaftaran Donor Darah Online

Project Laravel dengan tampilan Bootstrap.

## Persiapan

1. Salin `.env.example` menjadi `.env`.
2. Buat database MySQL dan sesuaikan konfigurasi `DB_*` di `.env`.
3. Install dependency PHP dan JavaScript:

   ```bash
   composer install
   npm install
   ```

4. Generate application key dan jalankan migration:

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. Build asset Bootstrap dan jalankan server:

   ```bash
   npm run build
   php artisan serve
   ```

Buka `http://127.0.0.1:8000`.

> File `.env`, folder `vendor`, dan folder `node_modules` tidak disertakan dalam ZIP. Jangan membagikan file `.env` karena dapat berisi kredensial database atau secret aplikasi.
