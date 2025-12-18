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
    // 1. Kebutuhan Isi Kamar Kos (Existing)
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

    // 2. Peralatan Gaming Dasar (Existing)
    'GAMING_DASAR' => [
        'name' => 'Peralatan Gaming Dasar',
        'items' => [
            'Mouse Gaming Ringan' => ['query' => 'lightweight gaming mouse', 'min_price_usd' => 30, 'max_price_usd' => 70],
            'Mousepad Besar' => ['query' => 'large gaming mouse pad', 'min_price_usd' => 15, 'max_price_usd' => 25],
            'Headset Gaming Stereo' => ['query' => 'stereo gaming headset budget', 'min_price_usd' => 20, 'max_price_usd' => 45],
        ],
    ],

    // 3. Content Creator Starter (Mobile)
    'CONTENT_CREATOR' => [
        'name' => 'Starter Pack Content Creator (HP)',
        'items' => [
            'Ring Light' => ['query' => 'ring light with tripod stand', 'min_price_usd' => 20, 'max_price_usd' => 40],
            'Clip-on Mic Wireless' => ['query' => 'wireless lavalier microphone smartphone', 'min_price_usd' => 15, 'max_price_usd' => 35],
            'Phone Gimbal' => ['query' => '3-axis smartphone gimbal stabilizer', 'min_price_usd' => 50, 'max_price_usd' => 90],
        ],
    ],

    // 4. Kebersihan Kamar Mandi
    'KEBERSIHAN_TOILET' => [
        'name' => 'Alat Kebersihan Kamar Mandi',
        'items' => [
            'Sikat Lantai Elektrik' => ['query' => 'electric spin scrubber', 'min_price_usd' => 25, 'max_price_usd' => 45],
            'Dispenser Sabun Otomatis' => ['query' => 'automatic soap dispenser', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Rak Gantung Handuk' => ['query' => 'towel rack wall mounted', 'min_price_usd' => 10, 'max_price_usd' => 30],
        ],
    ],

    // 5. Coffee Station di Kamar
    'COFFEE_STATION' => [
        'name' => 'Home Coffee Corner (Manual)',
        'items' => [
            'Manual Coffee Grinder' => ['query' => 'manual coffee bean grinder', 'min_price_usd' => 15, 'max_price_usd' => 40],
            'French Press' => ['query' => 'glass french press 600ml', 'min_price_usd' => 10, 'max_price_usd' => 25],
            'Timbangan Kopi Digital' => ['query' => 'digital coffee scale with timer', 'min_price_usd' => 12, 'max_price_usd' => 20],
        ],
    ],

    // 6. Work From Home (WFH) Ergonomis
    'WFH_ERGONOMIC' => [
        'name' => 'WFH Setup Ergonomis',
        'items' => [
            'Laptop Stand' => ['query' => 'aluminum adjustable laptop stand', 'min_price_usd' => 15, 'max_price_usd' => 35],
            'Keyboard Mechanical Wireless' => ['query' => 'wireless mechanical keyboard 75%', 'min_price_usd' => 40, 'max_price_usd' => 80],
            'Sandaran Punggung' => ['query' => 'ergonomic lumbar support memory foam', 'min_price_usd' => 15, 'max_price_usd' => 30],
        ],
    ],

    // 7. Olahraga Mandiri (Home Gym)
    'HOME_GYM' => [
        'name' => 'Olahraga Mandiri di Rumah',
        'items' => [
            'Matras Yoga' => ['query' => 'non-slip yoga mat thickened', 'min_price_usd' => 15, 'max_price_usd' => 30],
            'Dumbbell Set' => ['query' => 'adjustable dumbbell set 10kg', 'min_price_usd' => 30, 'max_price_usd' => 60],
            'Resistance Band Set' => ['query' => 'resistance bands set with handles', 'min_price_usd' => 10, 'max_price_usd' => 25],
        ],
    ],

    // 8. Perlengkapan Travelling (Short Trip)
    'TRAVELLING_LIGHT' => [
        'name' => 'Travelling Singkat (Backpacker)',
        'items' => [
            'Tas Ransel Kabin' => ['query' => '40L travel backpack carry on', 'min_price_usd' => 35, 'max_price_usd' => 70],
            'Packing Cubes' => ['query' => 'travel packing cubes set', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Powerbank 20000mAh' => ['query' => 'power bank 20000mah fast charging', 'min_price_usd' => 20, 'max_price_usd' => 45],
        ],
    ],

    // 9. Dapur Minimalis Kos (Masak Praktis)
    'DAPUR_MINIMALIS' => [
        'name' => 'Dapur Kos Praktis',
        'items' => [
            'Panci Elektrik Multifungsi' => ['query' => 'multi function electric cooker', 'min_price_usd' => 20, 'max_price_usd' => 40],
            'Air Fryer Kecil' => ['query' => 'mini air fryer 2L', 'min_price_usd' => 35, 'max_price_usd' => 60],
            'Pisau Set Dapur' => ['query' => 'kitchen knife set stainless steel', 'min_price_usd' => 15, 'max_price_usd' => 30],
        ],
    ],

    // 10. Perawatan Sepatu (Sneakerhead)
    'SNEAKER_CARE' => [
        'name' => 'Sneaker Care Kit',
        'items' => [
            'Kotak Sepatu Transparan' => ['query' => 'transparent stackable shoe box', 'min_price_usd' => 25, 'max_price_usd' => 50],
            'Pembersih Sepatu Set' => ['query' => 'sneaker cleaning kit brush', 'min_price_usd' => 10, 'max_price_usd' => 25],
            'Deodorizer Sepatu' => ['query' => 'shoe deodorizer balls', 'min_price_usd' => 5, 'max_price_usd' => 12],
        ],
    ],

    // 11. Smart Home Dasar
    'SMART_HOME_BUDGET' => [
        'name' => 'Smart Home Dasar',
        'items' => [
            'Smart Bulb RGB' => ['query' => 'smart wifi led bulb rgb', 'min_price_usd' => 10, 'max_price_usd' => 20],
            'Smart Plug' => ['query' => 'smart wifi plug socket', 'min_price_usd' => 8, 'max_price_usd' => 15],
            'Universal IR Remote' => ['query' => 'smart ir remote control wifi', 'min_price_usd' => 10, 'max_price_usd' => 20],
        ],
    ],

    // 12. Alat Pertukangan Dasar (DIY)
    'DIY_TOOLS' => [
        'name' => 'Alat Pertukangan Dasar',
        'items' => [
            'Obeng Set Presisi' => ['query' => 'precision screwdriver set repair kit', 'min_price_usd' => 12, 'max_price_usd' => 25],
            'Meteran Digital/Fisik' => ['query' => 'measuring tape 5m', 'min_price_usd' => 5, 'max_price_usd' => 15],
            'Palu Kambing' => ['query' => 'claw hammer steel', 'min_price_usd' => 8, 'max_price_usd' => 18],
        ],
    ],

    // 13. Kebersihan Udara Kamar
    'AIR_PURITY' => [
        'name' => 'Kesehatan Udara Kamar',
        'items' => [
            'Air Purifier Portable' => ['query' => 'portable HEPA air purifier', 'min_price_usd' => 35, 'max_price_usd' => 70],
            'Humidifier / Diffuser' => ['query' => 'ultrasonic aroma diffuser', 'min_price_usd' => 15, 'max_price_usd' => 30],
            'Higrometer Digital' => ['query' => 'digital humidity temperature monitor', 'min_price_usd' => 5, 'max_price_usd' => 12],
        ],
    ],

    // 14. Skincare Starter (Unisex)
    'SKINCARE_BASIC' => [
        'name' => 'Skincare Basic Starter',
        'items' => [
            'Sunscreen' => ['query' => 'sunscreen spf 50 face', 'min_price_usd' => 10, 'max_price_usd' => 25],
            'Facial Wash' => ['query' => 'gentle facial cleanser', 'min_price_usd' => 8, 'max_price_usd' => 20],
            'Moisturizer' => ['query' => 'face moisturizer hydrating', 'min_price_usd' => 12, 'max_price_usd' => 30],
        ],
    ],

    // 15. Keamanan Kamar (Security)
    'SECURITY_KIT' => [
        'name' => 'Sistem Keamanan Kamar',
        'items' => [
            'CCTV Wifi (IP Cam)' => ['query' => 'indoor wifi ip camera 1080p', 'min_price_usd' => 20, 'max_price_usd' => 45],
            'Sensor Pintu Wifi' => ['query' => 'wifi door window sensor', 'min_price_usd' => 5, 'max_price_usd' => 15],
            'Gembok Fingerprint' => ['query' => 'smart fingerprint padlock', 'min_price_usd' => 15, 'max_price_usd' => 30],
        ],
    ],
];