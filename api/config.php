<?php
// Mendefinisikan header agar PHP selalu merespons dalam format JSON
header('Content-Type: application/json');

// --- Fungsi untuk Mendapatkan API Key dengan Aman ---
function get_api_key($name) {
    // Ambil kunci dari Environment Variables (metode standar di hosting/CI)
    $key = getenv($name);
    if (!$key) {
        // Fallback untuk local testing jika tidak menggunakan environment variables
        // Dalam produksi, baris ini HARUS DIHAPUS.
        // Ganti 'YOUR_EBAY_APP_ID' dengan kunci Anda HANYA untuk local testing
        if ($name === 'EBAY_APP_ID') return 'YOUR_EBAY_APP_ID_FALLBACK';
        if ($name === 'CURRENCY_API_KEY') return 'YOUR_CURRENCY_API_KEY_FALLBACK';
    }
    return $key;
}

// --- Lookup Table untuk Kategori dan Prioritas ---
// Prioritas: 1 = WAJIB, 2 = Penting, 3 = Opsional. Digunakan dalam Logika Budgeting.
const BUNDLE_CATEGORIES = [
    'ISI_KAMAR_KOS' => [
        ['item' => 'Meja Belajar', 'query' => 'student study desk', 'priority' => 1],
        ['item' => 'Kursi Ergonomis', 'query' => 'basic ergonomic chair', 'priority' => 1],
        ['item' => 'Lampu Meja', 'query' => 'simple desk lamp', 'priority' => 2],
        ['item' => 'Rak Buku Kecil', 'query' => 'small bookshelf organizer', 'priority' => 3],
        ['item' => 'Tempat Pensil', 'query' => 'desk pencil holder and phone stand', 'priority' => 3],
        ['item' => 'Kipas Mini', 'query' => 'mini portable fan', 'priority' => 2],
        ['item' => 'Peralatan Mandi', 'query' => 'basic bathroom accessories set', 'priority' => 3],
    ],
    // Tambahkan kategori lain di sini (PARSEL NATAL, dll.)
];

// --- Konfigurasi URL API ---
const CURRENCY_API_URL = 'https://api.freecurrencyapi.com/v1/latest?apikey=';
const EBAY_API_URL = 'https://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsByKeywords&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=';

?>