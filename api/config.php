<?php
// =======================================================
// CONFIG.PHP - PENGATURAN GLOBAL DAN KUNCI API
// Tim A & C (DevOps)
// =======================================================

// --- DEFENISI KUNCI LOKAL UNTUK XAMPP (HARAP DIISI MANUAL) ---
// *PENTING: Nilai ini HANYA akan digunakan saat Anda menjalankan di XAMPP/localhost.*
// *JANGAN PERNAH MENGISI kunci asli Anda jika Anda mengunggah file ini ke repositori publik.*
$local_keys = [
    // GANTI string ini dengan App ID eBay Production Anda
    'EBAY_APP_ID' => 'Terijuki-BudgetBu-PRD-f14d589d7-dff4ee6c', 
    
    // GANTI string ini dengan Kunci API dari ExchangeRate-API atau penyedia kurs lainnya
    'CURRENCY_API_KEY' => '51a063e46fed3e28ecff07e8', 
];

/**
 * Mengambil Kunci API dari Environment Variables (CI/CD) atau Kunci Lokal (XAMPP).
 * * Logic: Prioritas utama adalah $_ENV (GitHub Secrets). Jika kosong, baru cek $local_keys.
 *
 * @param string $key Nama Environment Variable.
 * @return string Nilai kunci atau string kosong jika tidak ditemukan.
 */
function get_api_key(string $key): string {
    global $local_keys; // Akses kunci yang didefinisikan di atas

    // 1. Cek dari GitHub Secrets (Environment di CI/CD/Production)
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    
    // 2. Cek dari Kunci Lokal (Untuk XAMPP/Development)
    // Kami memeriksa apakah nilainya telah diganti dari placeholder default.
    if (isset($local_keys[$key]) && strpos($local_keys[$key], 'GANTI_DENGAN_KUNCI') === false) {
        return $local_keys[$key];
    }
    
    // 3. Fallback
    return '';
}

// -------------------------------------------------------
// KONFIGURASI URL API (Basis URL)
// -------------------------------------------------------
// Menggunakan API ini karena mendukung panggilan tanpa KEY, atau KEY terpisah (cocok untuk contoh ini)
const CURRENCY_API_URL = 'https://api.exchangerate-api.com/v4/latest/USD'; 
const EBAY_API_URL_BASE = 'https://svcs.ebay.com/services/search/FindingService/v1';


// -------------------------------------------------------
// GLOBAL LOOKUP TABLE: Kategori Produk dan Query eBay
// Tim C bertanggung jawab untuk merawat daftar ini.
// -------------------------------------------------------
const BUNDLE_CATEGORIES = [
    // BUNDLE 1: Kebutuhan Isi Kamar Kos
    'ISI_KAMAR_KOS' => [
        'name' => 'Kebutuhan Isi Kamar Kos (Hemat)',
        'items' => [
            'Meja Lipat Belajar' => ['query' => 'portable study table', 'min_price_usd' => 15, 'max_price_usd' => 30],
            'Rak Buku Minimalis' => ['query' => 'small minimalist bookshelf', 'min_price_usd' => 25, 'max_price_usd' => 50],
            'Lampu Meja LED' => ['query' => 'flexible LED desk lamp', 'min_price_usd' => 10, 'max_price_usd' => 20],
            'Peralatan Makan Set' => ['query' => 'reusable cutlery set', 'min_price_usd' => 5, 'max_price_usd' => 15],
            'Keset Kamar Mandi' => ['query' => 'absorbent bath mat', 'min_price_usd' => 8, 'max_price_usd' => 12],
        ],
    ],

    // BUNDLE 2: Peralatan Gaming Dasar
    'GAMING_DASAR' => [
        'name' => 'Peralatan Gaming Dasar',
        'items' => [
            'Mouse Gaming Ringan' => ['query' => 'lightweight gaming mouse', 'min_price_usd' => 30, 'max_price_usd' => 70],
            'Mousepad Besar' => ['query' => 'large gaming mouse pad', 'min_price_usd' => 15, 'max_price_usd' => 25],
            'Headset Gaming Stereo' => ['query' => 'stereo gaming headset budget', 'min_price_usd' => 20, 'max_price_usd' => 45],
        ],
    ],
];