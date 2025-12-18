# Bundle Creator Web App

## Deskripsi Proyek

Bundle Creator adalah aplikasi web berbasis PHP dan JavaScript untuk menghasilkan rekomendasi bundle produk berdasarkan kebutuhan dan budget pengguna.

## Fitur Utama

* Input tipe bundle dan budget
* Rekomendasi item otomatis dari API
* Penanda status item (dibeli / diabaikan)
* Tampilan responsif untuk mobile dan desktop

---

## Teknologi yang Digunakan

* PHP (Native)
* HTML, CSS, JavaScript
* PHP Built-in Server / XAMPP

---

## Struktur Folder


project-root/
├── api/
│   └── bundle_creator.php
├── public/
│   ├── index.html
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── js/
│           └── main.js
├── docs/
│   └── API_DOCS.md
└── README.md


---

## Cara Menjalankan Lokal

1. Pastikan PHP sudah terinstal
2. Masuk ke folder project


cd project-root


3. Jalankan server PHP


php -S localhost:8000


4. Akses aplikasi di browser


http://localhost:8000/public/index.html


---

## Catatan

* style.css mengatur tampilan dan status warna
* main.js menambahkan class status item
* API mengikuti kontrak pada docs/API_DOCS.md

---

