# Toko Online

<p align="center"><img src="public/backend/image/logo_ubsi.png" width="200" alt="Logo UBSI"></p>
<p align="center"><img src="public/backend/image/logo_text2.png" width="300" alt="Text Logo Project"></p>


## Pengenalan Project

Project ini merupakan project Laravel pada mata kuliah Web Programming II di Universitas Bina Sarana Informatika yang bertemakan tentang Toko Online.

## Relasi Tabel
<p align="center"><img src="public/backend/image/screenshot/ss_relasi.jpg" width="600" alt="Laravel Logo"></p>

## Pra-Install
Sebelumnya pastikan menginstall terlebih dahulu kebutuhan sistem untuk menggunakan project ini.
- [x] Git
- [x] Composer
- [x] Code Editor <code>VS Code, Sublime Text atau sejenisnya</code>
- [x] Web Server <code>Laragon, Xampp atau sejenisnya</code>
- [x] Web Browser <code>Chrome, Mozilla atau sejenisnya</code>
- [x] Node.js <code>(opsional)</code>

## Cara Install
1. Cloning repository ini melalui terminal. Tunggu hingga selesai cloning.
```
2.  Buka project menggunakan Code Editor, lalu inisiasi dependensi composer melalui terminal.
```
composer install
```
3. Duplikat file `.env.example` menjadi `.env` dan konfigurasikan koneksi database menjadi seperti ini pada file `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_tokoonline
DB_USERNAME=root
DB_PASSWORD=
```
4. Generate key aplikasi pada env melalui terminal.
```
php artisan key:generate
```
5. Generate Storage Link melalui terminal agar dapat mengakses storage.
```
php artisan storage:link
```
6. Migrasikan tabel beserta seeder melalui terminal.
```
php artisan migrate:fresh --seed
```
