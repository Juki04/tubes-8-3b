# 📋 REQUIREMENTS DOCUMENT
Project: Bundle Creator Application  
Tim: D – Documentation & Design Specialist  

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini menjelaskan kebutuhan fungsional dan non-fungsional aplikasi Bundle Creator pada aspek tampilan dan dokumentasi.

### 1.2 Ruang Lingkup
Ruang lingkup meliputi:
- Tampilan antarmuka pengguna (UI)
- Styling dan responsivitas
- Visualisasi status hasil bundle
- Dokumentasi API
- Dokumentasi penggunaan aplikasi

---

## 2. Stakeholder

| Peran | Keterangan |
|------|-----------|
| User | Pengguna aplikasi |
| Developer | Tim A, B, C |
| Documentation & Design | Tim D |
| Dosen / Penguji | Penilai sistem |

---

## 3. Functional Requirements (FR)

### FR-01 — Tampilan Halaman Utama
Sistem harus menampilkan halaman utama berisi form input dan tombol aksi.

**Kriteria Penerimaan:**
- Judul aplikasi terlihat
- Form input budget dan tipe bundle tersedia
- Tombol generate dapat digunakan

---

### FR-02 — Layout Responsif
Sistem harus menyesuaikan tampilan untuk desktop dan mobile.

**Kriteria Penerimaan:**
- Desktop: elemen sejajar rapi
- Mobile: elemen tersusun vertikal
- Tidak ada elemen terpotong

---

### FR-03 — Styling dan Branding
Sistem harus menggunakan styling yang konsisten.

**Kriteria Penerimaan:**
- Warna dan font konsisten
- Spacing rapi
- Kontras teks jelas

---

### FR-04 — Visual Status Hasil Bundle
Sistem harus menampilkan status hasil bundle secara visual.

**Kriteria Penerimaan:**
- Status dibeli berwarna hijau
- Status diabaikan berwarna merah
- Status mudah dikenali pengguna

---

### FR-05 — Dokumentasi API
Sistem harus menyediakan dokumentasi endpoint API.

**Kriteria Penerimaan:**
- Endpoint `GET /api/bundle_creator.php` terdokumentasi
- Parameter input dijelaskan
- Contoh response sukses dan gagal tersedia

---

### FR-06 — Dokumentasi Proyek
Sistem harus memiliki README sebagai panduan penggunaan.

**Kriteria Penerimaan:**
- Deskripsi proyek tersedia
- Cara menjalankan lokal dijelaskan
- Teknologi yang digunakan disebutkan

---

## 4. Non-Functional Requirements (NFR)

### NFR-01 — Usability
- Tampilan mudah dipahami
- Pengguna tidak memerlukan panduan tambahan

---

### NFR-02 — Maintainability
- CSS terpisah dari HTML
- Dokumentasi mudah diperbarui

---

### NFR-03 — Compatibility
- Berjalan di browser modern
- Tidak membutuhkan plugin tambahan

---

### NFR-04 — Readability
- Dokumentasi menggunakan bahasa yang jelas
- Struktur Markdown rapi

---

## 5. Traceability

| Requirement | Test Case |
|------------|-----------|
| FR-01 | BB-01 |
| FR-02 | BB-02, BB-03 |
| FR-03 | BB-04 |
| FR-04 | BB-05, BB-06 |
| FR-05 | WB-05 |
| FR-06 | WB-06 |

---

## 6. Penutup
Dokumen ini menjadi acuan pengembangan dan pengujian aplikasi Bundle Creator pada aspek tampilan dan dokumentasi.
