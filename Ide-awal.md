# 🗺️ ROADMAP PROYEK: PERSONALIZED BUNDLE & BUDGET PLANNER

**Tujuan Utama:** Membuat REST Client dengan PHP yang mengintegrasikan eBay Finding API dan Currency API, serta menerapkan logika *budgeting* untuk merekomendasikan "Bundle Produk" (misalnya: Isi Kamar Kos) dalam mata uang Rupiah.

**Stack Teknologi:** HTML, CSS (Bootstrap/Tailwind), JavaScript, PHP (Backend Logic), GitHub Actions (CI/CD).

---

## 🧑‍💻 KATEGORI 1: BACKEND (PHP LOGIC & API INTEGRATION)

**Tanggung Jawab:** Membangun *endpoint* utama (`/api/bundle_creator.php`) dan semua logika pemrosesan data.

### 1.1. Setup Dasar & Koneksi API
* [ ] Siapkan struktur folder dasar (misalnya: `public/`, `api/`, `.github/`).
* [ ] Dapatkan dan simpan **API Key eBay** dan **API Key Free Currency** sebagai *Environment Variables* di server lokal dan di GitHub Secrets (untuk CI/CD).
* [ ] Buat *script* PHP dasar (`/api/bundle_creator.php`) untuk menerima parameter: `bundle_type` dan `budget_usd`.

### 1.2. Logika Kategorisasi & Query
* [ ] Definisikan **PHP Lookup Table** untuk kategori dan item default (Lihat Bagian **Kategori dan Item Default** di bawah).
* [ ] Tulis fungsi PHP untuk menerjemahkan `bundle_type` menjadi *array* **multiple eBay queries** (misal: "Isi Kamar Kos" -> ["desk", "ergonomic chair", ...]).

### 1.3. Integrasi Multi-API & Pemrosesan Data (KUNCI INOVASI)
* [ ] Implementasikan **panggilan cURL** PHP untuk mengambil nilai tukar **USD ke IDR** dari Currency API.
* [ ] Tulis *loop* untuk memanggil **eBay Finding API** untuk **setiap *query* item** yang dihasilkan.
* [ ] Lakukan **Error Handling** di PHP (jika salah satu API gagal, proses harus tetap berlanjut dengan pesan *fallback*).

### 1.4. Logika Budgeting Cerdas
* [ ] Implementasikan logika di PHP untuk **mengonversi** harga eBay (USD) ke IDR.
* [ ] Tulis logika **Prioritas & Budgeting**: Iterasi melalui semua item yang ditemukan. Jika total harga melebihi `budget_usd` yang diinput, tentukan *item* mana yang harus di-*skip* (misalnya: item yang paling mahal) dan tandai statusnya sebagai **"Disarankan/Diabaikan"**.

### 1.5. Output Akhir
* [ ] Format semua data yang terkumpul (Item, Harga IDR, Link, Status Pembelian) ke dalam **satu *response* JSON tunggal** yang bersih. 

---

## 🎨 KATEGORI 2: FRONTEND (HTML, CSS, JAVASCRIPT)

**Tanggung Jawab:** Membangun antarmuka pengguna yang responsif dan menampilkan hasil dengan jelas.

### 2.1. Struktur & Styling
* [ ] Buat file `index.html` dan sambungkan dengan framework CSS pilihan (Bootstrap/Tailwind).
* [ ] Rancang formulir input sederhana untuk **Tipe Bundle** (dropdown/radio) dan **Budget Maksimum** (input angka).

### 2.2. JavaScript (Client-Side Logic)
* [ ] Tulis fungsi JS untuk menangani *event submit* dari formulir.
* [ ] Gunakan **`fetch()` API** untuk mengirim permintaan `bundle_type` dan `budget_usd` ke *endpoint* PHP (`/api/bundle_creator.php`).

### 2.3. Data Presentation
* [ ] Tulis fungsi JS untuk menerima dan memproses *response* JSON dari PHP.
* [ ] Tampilkan hasil dalam format **Tabel Komparasi** yang jelas, dengan kolom:
    * Nama Item
    * Harga Awal (USD)
    * Harga Akhir (IDR)
    * Status (**Dibeli** / **Diabaikan**)
    * Link Produk (Hyperlink ke eBay)
* [ ] Tampilkan **Total Harga Akhir** dari semua item yang **"Dibeli"**.

---

## 🤖 KATEGORI 3: CI/CD (GITHUB WORKFLOW)

**Tanggung Jawab:** Memastikan kualitas kode, keamanan, dan kesiapan *deployment*.

### 3.1. Workflow Setup
* [ ] Buat file GitHub Actions (`.github/workflows/ci.yml`).
* [ ] Konfigurasi *job* untuk berjalan pada *push* atau *pull request* ke *branch* utama (`main`).

### 3.2. Static Analysis & Linting
* [ ] Tambahkan *step* untuk menjalankan **Linting/Static Analysis** pada kode PHP dan JavaScript.

### 3.3. Integration Testing (KUNCI NILAI TINGGI)
* [ ] Buat *testing script* sederhana (misalnya, *curl* atau PHP *script* dasar) yang secara otomatis memanggil *endpoint* `/api/bundle_creator.php` dengan *dummy data*.
* [ ] Tambahkan *step* **Assertion**: Pastikan *response* JSON selalu memiliki kunci data yang diperlukan (misalnya: `item_name`, `price_idr`, `status`) dan kode statusnya $200$.
* [ ] **Test Logika Budgeting:** Tambahkan *test case* khusus untuk memverifikasi bahwa ketika budget kecil, *item* yang mahal ditandai sebagai **"Diabaikan"**.

### 3.4. Security Checks
* [ ] Pastikan *workflow* mengamankan akses ke **API Keys** melalui GitHub Secrets.

---

## 📚 KATEGORI 4: DOKUMENTASI

**Tanggung Jawab:** Menulis dokumentasi teknis dan panduan penggunaan.

* [ ] Buat file `README.md` yang menjelaskan tujuan proyek, cara menjalankan lokal, dan API yang digunakan.
* [ ] Buat `API_DOCS.md` yang mendokumentasikan *endpoint* REST Client baru Anda (`/api/bundle_creator.php`), termasuk parameter input dan *schema* output JSON yang diharapkan.
* [ ] Tulis panduan singkat mengenai alasan memilih struktur CI/CD yang diterapkan (untuk presentasi).

---

## 🛒 KATEGORI DAN ITEM DEFAULT (LOGIKA PHP)

| Bundle Type (`bundle_type` input) | Item Kunci (Default) | Query eBay yang Digunakan |
| :--- | :--- | :--- |
| **ISI KAMAR KOS** | Meja Belajar | `student study desk` |
| | Kursi Ergonomis | `basic ergonomic chair` |
| | Lampu Meja | `simple desk lamp` |
| | Rak Buku Kecil | `small bookshelf organizer` |
| | Tempat Pensil/Holder HP | `desk pencil holder and phone stand` |
| | Kipas Mini | `mini portable fan` |
| | Set Peralatan Mandi | `basic bathroom accessories set` |
| **PARSEL NATAL** | Mug Kopi/Teh | `christmas themed mug set` |
| | Lilin Aromaterapi | `holiday scented candle gift` |
| | Cokelat Premium | `gourmet chocolate box` |
| | Kartu Ucapan | `festive greeting cards lot` |
| | Hiasan Natal Kecil | `small christmas ornament decorative` |
| **PARSEL HADIAH KHUSUS** | Kotak Penyimpanan | `decorative storage box` |
| | Item Hobi Acak | `random hobby item kit` |
| | Alat Tulis Premium | `premium writing pens set` |


## 👥 Pembagian Tugas Awal (Initial Task Allocation)


| Anggota Tim | Fokus Utama | Tugas Kunci (Minggu Pertama) |
| :--- | :--- | :--- |
| **Tim A (Backend Lead - Anda)** | Kategori 1: Backend Logic & Integrasi API | Mengatur *setup* proyek, mendaftarkan API Keys (eBay & Currency), membuat skema JSON *response* akhir, dan menulis logika konversi mata uang dasar. |
| **Tim B (Frontend Lead)** | Kategori 2: Frontend & JavaScript Logic | Merancang `index.html`, memilih *styling framework* (Tailwind/Bootstrap), dan menulis fungsi JavaScript untuk mengirim *request* ke *endpoint* PHP. |
| **Tim C (DevOps/QA Lead)** | Kategori 3: CI/CD & Testing | Mengatur GitHub Repository, membuat *file* `.github/workflows/ci.yml`, dan menulis *testing script* dasar untuk memverifikasi *response* PHP. |
| **Tim D (Documentation/UI)** | Kategori 4: Dokumentasi & Desain UI | Menyusun `README.md` dan `API_DOCS.md`, serta membuat *mockup* (sketsa) tampilan *frontend* untuk diserahkan ke Tim B. |

---


📋 Penjelasan Struktur File
1. 📁 Folder Utama (bundle-budget-planner/)
README.md: Deskripsi proyek, cara instalasi lokal, dan panduan penggunaan.

roadmap.md: Dokumen perencanaan kerja tim (yang baru saja kita buat).

2. 🔑 Folder api/ (Backend PHP Logic - Tim A)
Tempat semua logika server-side berada.

bundle_creator.php: Ini adalah REST Client Anda. File ini bertanggung jawab untuk:

Menerima input (bundle_type, budget_usd).

Memanggil Currency API.

Melakukan loop untuk memanggil eBay API.

Menerapkan Logika Budgeting Cerdas dan Prioritas.

Menghasilkan response JSON akhir.

config.php: Berisi definisi lookup table (kategori dan query eBay) dan konfigurasi umum yang non-sensitif. PENTING: File ini tidak boleh menyimpan API Keys, API Keys harus diakses dari Environment Variables.

.htaccess: (Opsional) Digunakan jika Anda ingin membuat clean URL atau menambahkan header keamanan dasar (misalnya: CORS).

3. 🌐 Folder public/ (Frontend - Tim B & D)
Ini adalah akar website yang dapat diakses oleh browser.

index.html: Satu-satunya halaman HTML. Berisi formulir input dan tempat untuk menampilkan tabel hasil.

assets/css/style.css: File CSS utama (baik kustom maupun output jika Anda menggunakan Tailwind/SASS).

assets/js/main.js: Kode JavaScript utama yang mengirim fetch() ke /api/bundle_creator.php dan merender hasil JSON ke tabel HTML.

assets/js/utils.js: (Opsional) Untuk fungsi pembantu seperti formatCurrency(amount, 'IDR').

4. 🤖 Folder .github/workflows/ (CI/CD - Tim C)
ci.yml: Berisi langkah-langkah workflow GitHub Actions: linting, static analysis PHP, dan yang terpenting, Integration Testing untuk memastikan endpoint Anda berfungsi.

5. 📚 Folder docs/ (Dokumentasi - Tim D)
API_DOCS.md: Dokumentasi teknis dari endpoint yang Anda buat (bundle_creator.php), termasuk contoh request dan response JSON.

requirements.md: Detail teknis seperti daftar API Keys, versi PHP minimum, dan instruksi setup lingkungan lokal.