<?php
// =======================================================
// CONFIG.PHP - PENGATURAN GLOBAL DAN KUNCI API
// Tim A & C (DevOps)
// =======================================================

// --- DEFENISI KUNCI LOKAL UNTUK XAMPP (HARAP DIISI MANUAL) ---
// *PENTING: Nilai ini HANYA akan digunakan saat Anda menjalankan di XAMPP/localhost.*
$local_keys = [
    // GANTI string ini dengan App ID eBay Production Anda
    'EBAY_APP_ID' => 'Terijuki-BudgetBu-SBX-0e8d9c741-4b438d50', 
    
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
const EBAY_API_URL_BASE = 'https://svcs.sandbox.ebay.com/services/search/FindingService/v1';


// -------------------------------------------------------
// GLOBAL LOOKUP TABLE: Kategori Produk dan Query eBay
// Tim C bertanggung jawab untuk merawat daftar ini.
// -------------------------------------------------------
const BUNDLE_CATEGORIES = [
    // 1. Kebutuhan Isi Kamar Kos (Existing)
    'ISI_KAMAR_KOS' => [
        'name' => 'Kebutuhan Isi Kamar Kos (Hemat)',
        'items' => [
            'Meja Lipat Belajar' => ['query' => 'table', 'min_price_usd' => 15, 'max_price_usd' => 30],
            'Rak Buku Minimalis' => ['query' => 'bookshelf', 'min_price_usd' => 25, 'max_price_usd' => 50],
            'Lampu Meja LED' => ['query' => 'desk lamp', 'min_price_usd' => 10, 'max_price_usd' => 20],
            'Peralatan Makan Set' => ['query' => 'cutlery set', 'min_price_usd' => 5, 'max_price_usd' => 15],
            'Keset Kamar Mandi' => ['query' => 'mat', 'min_price_usd' => 8, 'max_price_usd' => 12],
        ],
    ],

    // 2. Peralatan Gaming Dasar (Existing)
    'GAMING_DASAR' => [
        'name' => 'Peralatan Gaming Dasar',
        'items' => [
            'Mouse Gaming Ringan' => ['query' => 'gaming mouse', 'min_price_usd' => 30, 'max_price_usd' => 70],
            'Mousepad Besar' => ['query' => 'mouse pad', 'min_price_usd' => 15, 'max_price_usd' => 25],
        ],
    ],

    // 3. Content Creator Starter (Mobile)
    'CONTENT_CREATOR' => [
        'name' => 'Starter Pack Content Creator (HP)',
        'items' => [
            'Ring Light' => ['query' => 'Ring Light', 'min_price_usd' => 20, 'max_price_usd' => 40],
            'Clip-on Mic Wireless' => ['query' => 'smartphone', 'min_price_usd' => 15, 'max_price_usd' => 35],
            'Phone Gimbal' => ['query' => 'Phone Gimbal', 'min_price_usd' => 50, 'max_price_usd' => 90],
        ],
    ],

    // 4. Kebersihan Kamar Mandi
    'KEBERSIHAN_TOILET' => [
        'name' => 'Alat Kebersihan Kamar Mandi',
        'items' => [
            'Dispenser Sabun Otomatis' => ['query' => 'dispenser', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Rak Gantung Handuk' => ['query' => 'wall mounted', 'min_price_usd' => 10, 'max_price_usd' => 30],
        ],
    ],

    // 5. Coffee Station di Kamar
    'COFFEE_STATION' => [
        'name' => 'Home Coffee Corner (Manual)',
        'items' => [
            'Manual Coffee Grinder' => ['query' => 'grinder', 'min_price_usd' => 15, 'max_price_usd' => 40],
            'French Press' => ['query' => '600ml', 'min_price_usd' => 10, 'max_price_usd' => 25],
        ],
    ],

    // 6. Work From Home (WFH) Ergonomis
    'WFH_ERGONOMIC' => [
        'name' => 'WFH Setup Ergonomis',
        'items' => [
            'Laptop Stand' => ['query' => 'Laptop Stand', 'min_price_usd' => 15, 'max_price_usd' => 35],
            'Keyboard Mechanical Wireless' => ['query' => 'Keyboard Mechanical Wireless', 'min_price_usd' => 40, 'max_price_usd' => 80],
            'Sandaran Punggung' => ['query' => 'memory foam', 'min_price_usd' => 15, 'max_price_usd' => 30],
        ],
    ],

    // 7. Olahraga Mandiri (Home Gym)
    'HOME_GYM' => [
        'name' => 'Olahraga Mandiri di Rumah',
        'items' => [
            'Dumbbell Set' => ['query' => 'Dumbbell Set', 'min_price_usd' => 30, 'max_price_usd' => 60],
            'Resistance Band Set' => ['query' => 'Resistance Band Set', 'min_price_usd' => 10, 'max_price_usd' => 25],
        ],
    ],

    // 8. Perlengkapan Travelling (Short Trip)
    'TRAVELLING_LIGHT' => [
        'name' => 'Travelling Singkat (Backpacker)',
        'items' => [
            'Tas Ransel Kabin' => ['query' => 'carry on', 'min_price_usd' => 35, 'max_price_usd' => 70],
            'Packing Cubes' => ['query' => 'set', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Powerbank 20000mAh' => ['query' => 'fast charging', 'min_price_usd' => 20, 'max_price_usd' => 45],
        ],
    ],

    // 9. Dapur Minimalis Kos (Masak Praktis)
    'DAPUR_MINIMALIS' => [
        'name' => 'Dapur Kos Praktis',
        'items' => [
            'Panci Elektrik Multifungsi' => ['query' => 'electric cooker', 'min_price_usd' => 20, 'max_price_usd' => 40],
            'Air Fryer Kecil' => ['query' => '2L', 'min_price_usd' => 35, 'max_price_usd' => 60],
            'Pisau Set Dapur' => ['query' => 'stainless steel', 'min_price_usd' => 15, 'max_price_usd' => 30],
        ],
    ],

    // 10. Perawatan Sepatu (Sneakerhead)
    'SNEAKER_CARE' => [
        'name' => 'Sneaker Care Kit',
        'items' => [
            'Pembersih Sepatu Set' => ['query' => 'brush', 'min_price_usd' => 10, 'max_price_usd' => 25],
            'Deodorizer Sepatu' => ['query' => 'balls', 'min_price_usd' => 5, 'max_price_usd' => 12],
        ],
    ],

    // 11. Smart Home Dasar
    'SMART_HOME_BUDGET' => [
        'name' => 'Smart Home Dasar',
        'items' => [
            'Smart Bulb RGB' => ['query' => 'rgb', 'min_price_usd' => 10, 'max_price_usd' => 20],
            'Smart Plug' => ['query' => 'socket', 'min_price_usd' => 8, 'max_price_usd' => 15],
            'Universal IR Remote' => ['query' => 'wifi', 'min_price_usd' => 10, 'max_price_usd' => 20],
        ],
    ],

    // 12. Alat Pertukangan Dasar (DIY)
    'DIY_TOOLS' => [
        'name' => 'Alat Pertukangan Dasar',
        'items' => [
            'Obeng Set Presisi' => ['query' => 'repair kit', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Meteran Digital/Fisik' => ['query' => '5m', 'min_price_usd' => 5, 'max_price_usd' => 15],
            'Palu Kambing' => ['query' => 'steel', 'min_price_usd' => 8, 'max_price_usd' => 18],
        ],
    ],

    // 13. Kebersihan Udara Kamar
    'AIR_PURITY' => [
        'name' => 'Kesehatan Udara Kamar',
        'items' => [
            'Air Purifier Portable' => ['query' => 'air purifier', 'min_price_usd' => 35, 'max_price_usd' => 70],
            'Humidifier / Diffuser' => ['query' => 'diffuser', 'min_price_usd' => 15, 'max_price_usd' => 30],
            'Higrometer Digital' => ['query' => 'monitor', 'min_price_usd' => 5, 'max_price_usd' => 12],
        ],
    ],

    // 14. Skincare Starter (Unisex)
    'SKINCARE_BASIC' => [
        'name' => 'Skincare Basic Starter',
        'items' => [
            'Sunscreen' => ['query' => '50 face', 'min_price_usd' => 10, 'max_price_usd' => 25],
            'Facial Wash' => ['query' => 'cleanser', 'min_price_usd' => 8, 'max_price_usd' => 20],
            'Moisturizer' => ['query' => 'Moisturizer', 'min_price_usd' => 12, 'max_price_usd' => 30],
        ],
    ],

    // 15. Keamanan Kamar (Security)
    'SECURITY_KIT' => [
        'name' => 'Sistem Keamanan Kamar',
        'items' => [
            'CCTV Wifi (IP Cam)' => ['query' => 'camera 1080p', 'min_price_usd' => 20, 'max_price_usd' => 45],
            'Sensor Pintu Wifi' => ['query' => 'sensor', 'min_price_usd' => 5, 'max_price_usd' => 15],
        ],
    ],
];