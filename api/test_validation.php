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
if (file_exists('bundle_creator.php')) {
    echo "    ✅ PASS: File bundle_creator.php ditemukan.\n";
} else {
    echo "    ❌ FAIL: File bundle_creator.php tidak ditemukan.\n";
    exit(1);
}

/**
 * 2. Valid Syntax Test
 * Memastikan tidak ada error sintaksis (linting) pada file backend.
 */
echo " > Testing Valid Syntax...\n";
$output = shell_exec('php -l bundle_creator.php');
if (strpos($output, 'No syntax errors detected') !== false) {
    echo "    ✅ PASS: Sintaks PHP valid.\n";
} else {
    echo "    ❌ FAIL: Terdapat error sintaks pada file.\n";
    exit(1);
}

/**
 * 3. API Key Tidak Boleh Kosong
 * Mensimulasikan request tanpa kredensial keamanan.
 */
run_test(
    $endpoint . '?bundle_type=ISI_KAMAR_KOS&budget_usd=100', // Tanpa parameter api_key
    'API Key Kosong'
);

/**
 * 4. Tipe Bundle Invalid
 * Memastikan sistem menolak tipe bundle yang tidak terdaftar di BUNDLE_CATEGORIES.
 */
run_test(
    $endpoint . '?bundle_type=UNKNOWN_TYPE&budget_usd=100&api_key=secret123', 
    'Tipe Bundle Tidak Dikenali'
);

/**
 * 5. Budget Non-Numerik
 * Memastikan sistem menolak input budget yang berisi karakter selain angka.
 */
run_test(
    $endpoint . '?bundle_type=ISI_KAMAR_KOS&budget_usd=abc&api_key=secret123', 
    'Budget Bukan Angka'
);