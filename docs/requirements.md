# REQUIREMENTS DOCUMENT
Project: Bundle Creator Application  
Role: Tim D – Documentation & Design Specialist  

---

## 1. Purpose
Dokumen ini mendefinisikan kebutuhan fungsional dan non-fungsional aplikasi Bundle Creator pada aspek **tampilan (UI)** dan **dokumentasi**.

---

## 2. Scope
Cakupan requirements meliputi:
- Antarmuka pengguna (UI)
- Styling dan responsivitas
- Visualisasi status hasil bundle
- Dokumentasi API
- Dokumentasi penggunaan aplikasi

---

## 3. Stakeholders
| Role | Description |
|------|-------------|
| User | Pengguna aplikasi |
| Developer | Tim A, B, C |
| Documentation & Design | Tim D |
| Evaluator | Dosen / Penguji |

---

## 4. Functional Requirements

### FR-01 — Main Page UI
Sistem harus menampilkan halaman utama berisi form input dan tombol aksi.

**Acceptance Criteria:**
- Judul aplikasi terlihat
- Input budget dan tipe bundle tersedia
- Tombol generate dapat digunakan

---

### FR-02 — Responsive Layout
Sistem harus mendukung tampilan desktop dan mobile.

**Acceptance Criteria:**
- Desktop: elemen tersusun sejajar dan rapi
- Mobile: elemen tersusun vertikal
- Tidak ada elemen terpotong

---

### FR-03 — Consistent Styling
Sistem harus menggunakan styling yang konsisten dan profesional.

**Acceptance Criteria:**
- Warna dan font konsisten
- Spacing rapi
- Kontras teks jelas

---

### FR-04 — Visual Status Indicator
Sistem harus menampilkan status hasil bundle secara visual.

**Acceptance Criteria:**
- Status “dibeli” ditampilkan dengan warna hijau
- Status “diabaikan” ditampilkan dengan warna merah
- Status mudah dikenali pengguna

---

### FR-05 — API Documentation
Sistem harus menyediakan dokumentasi endpoint API.

**Acceptance Criteria:**
- Endpoint `GET /api/bundle_creator.php` terdokumentasi
- Parameter input dijelaskan
- Contoh response sukses dan gagal tersedia

---

### FR-06 — Project Documentation
Sistem harus memiliki README sebagai panduan penggunaan aplikasi.

**Acceptance Criteria:**
- Deskripsi proyek tersedia
- Cara menjalankan aplikasi secara lokal dijelaskan
- Teknologi yang digunakan dicantumkan

---

## 5. Non-Functional Requirements

### NFR-01 — Usability
- Antarmuka mudah dipahami
- Pengguna tidak memerlukan panduan tambahan

---

### NFR-02 — Maintainability
- CSS dipisahkan dari HTML
- Dokumentasi mudah diperbarui

---

### NFR-03 — Compatibility
- Berjalan di browser modern
- Tidak membutuhkan plugin tambahan

---

### NFR-04 — Readability
- Dokumentasi menggunakan bahasa yang jelas
- Struktur Markdown rapi dan konsisten

---

## 6. Traceability Matrix
| Requirement | Test Case |
|-------------|-----------|
| FR-01 | BB-01 |
| FR-02 | BB-02, BB-03 |
| FR-03 | BB-04 |
| FR-04 | BB-05, BB-06 |
| FR-05 | WB-05 |
| FR-06 | WB-06 |

---

## 7. Conclusion
Dokumen ini menjadi acuan pengembangan dan pengujian aplikasi Bundle Creator pada aspek tampilan dan dokumentasi.

