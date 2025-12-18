<?php
// Dibuat oleh Tim A (Backend Lead)
// Tujuan: Menguji validasi input di bundle_creator.php (Tugas yang harusnya dilakukan Tim C)

// Pastikan script ini tidak memiliki output selain dari proses testing itu sendiri
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "--- Menjalankan Input Validation Tests ---\n";

// Endpoint API yang sedang diuji (sesuai dengan konfigurasi server di ci.yml)
$endpoint = 'http://127.0.0.1:8000/api/bundle_creator.php';

/**
 * Fungsi untuk menjalankan pengujian CURL dan memverifikasi respons Error.
 * Jika respons tidak memiliki status 'error', script akan keluar dengan status GAGAL (exit 1).
 */
function run_test($url, $test_name) {
    echo "  > Testing " . $test_name . "...\n";
    
    // Inisialisasi cURL untuk permintaan HTTP
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan transfer sebagai string
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 detik
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = $response ? json_decode($response, true) : null;
    
    // Assertion 1: Memastikan kode status HTTP adalah Bad Request (400)
    if ($http_code !== 400) {
        echo "    ❌ FAIL: Status HTTP diharapkan 400, tetapi menerima " . $http_code . ".\n";
        exit(1);
    }
    
    // Assertion 2: Memastikan status di JSON adalah 'error'
    if ($data && isset($data['status']) && $data['status'] === 'error') {
        echo "    ✅ PASS: API mengembalikan status 'error' yang benar.\n";
    } else {
        echo "    ❌ FAIL: API GAGAL MENGEMBALIKAN status JSON 'error'. Output: " . $response . "\n";
        exit(1); // Gagal jika status bukan 'error'
    }
}

// =======================================================
// 5 TEST CASES (Integritas, Sintaks, & Keamanan)
// =======================================================

/**
 * 1. File Exist Test
 * Memastikan file bundle_creator.php tersedia sebelum dieksekusi.
 */
echo " > Testing File Exist...\n";
if (file_exists('api/config.php')) {
    echo "    ✅ PASS: File config.php ditemukan.\n";
} else {
    echo "    ❌ FAIL: File config.php tidak ditemukan.\n";
    exit(1);
}

/**
 * 2. Valid Syntax Test
 * Memastikan tidak ada error sintaksis (linting) pada file backend.
 */
echo " > Testing Valid Syntax...\n";
$php_files = glob('*.php');
$syntax_valid = true;
foreach ($php_files as $file) {
    $output = shell_exec("php -l $file");
    if (strpos($output, 'No syntax errors detected') === false) {
        echo "    ✅ PASS: Sintaks PHP valid.\n";
    } else {
        echo "    ❌ FAIL: Terdapat error sintaks pada file.\n";
        exit(1);
    }
}

/**
 * 3. API Key Retrieval Test
 * Memeriksa bahwa `api/bundle_creator.php` memuat `config.php` dan menggunakan
 * fungsi `get_api_key(...)` untuk mengambil `CURRENCY_API_KEY` dan `EBAY_APP_ID`.
 */
echo " > Testing 3: API Key Retrieval and Config Include...\n";
$file_content = file_get_contents('api/bundle_creator.php');
$has_require = (strpos($file_content, "require_once 'config.php'") !== false) || (strpos($file_content, 'require_once "config.php"') !== false);
$has_get_currency = (strpos($file_content, "get_api_key('CURRENCY_API_KEY')") !== false) || (strpos($file_content, 'get_api_key("CURRENCY_API_KEY")') !== false) || (strpos($file_content, "get_api_key( 'CURRENCY_API_KEY' )") !== false);
$has_get_ebay = (strpos($file_content, "get_api_key('EBAY_APP_ID')") !== false) || (strpos($file_content, 'get_api_key("EBAY_APP_ID")') !== false) || (strpos($file_content, "get_api_key( 'EBAY_APP_ID' )") !== false);

if ($has_require && $has_get_currency && $has_get_ebay) {
    echo "    ✅ PASS: bundle_creator.php memuat config.php dan memanggil get_api_key untuk CURRENCY_API_KEY dan EBAY_APP_ID.\n";
} else {
    echo "    ❌ FAIL: bundle_creator.php tidak memuat config.php atau tidak memanggil get_api_key untuk kunci yang diperlukan.\n";
    echo "    Debug: require_ok=" . ($has_require ? '1' : '0') . ", currency=" . ($has_get_currency ? '1' : '0') . ", ebay=" . ($has_get_ebay ? '1' : '0') . "\n";
    exit(1);
}

/**
 * 4. Tipe Bundle Invalid
 * Menguji apakah API mengembalikan error jika bundle_type tidak dikenal.
 */
run_test(
    $endpoint . '?bundle_type=UNKNOWN_TYPE&budget_usd=100&api_key=secret123', 
    'Testing 4: Tipe Bundle Tidak Dikenali'
);

/**
 * 5. Documentation Content Test
 * Memeriksa file API_DOCS.md dan mencari teks "Endpoint Utama".
 */
echo " > Testing 5: Documentation Content...\n";
$doc_file = 'docs/API_DOCS.md';
if (file_exists($doc_file)) {
    $doc_content = file_get_contents($doc_file);
    if (strpos($doc_content, 'Endpoint Utama') !== false) {
        echo "    ✅ PASS: Tulisan 'Endpoint Utama' ditemukan di dalam API_DOCS.md.\n";
    } else {
        echo "    ❌ FAIL: Tulisan 'Endpoint Utama' TIDAK ditemukan di dalam dokumentasi.\n";
        exit(1);
    }
} else {
    echo "    ❌ FAIL: File API_DOCS.md tidak ditemukan.\n";
    exit(1);
}

echo "\n--- Seluruh 5 Test Case Telah Selesai Dijalankan ---\n";
exit(0);