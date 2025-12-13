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
// TEST CASES
// =======================================================

// Test Case 1: Budget USD adalah Nol (Budget harus positif)
run_test(
    $endpoint . '?bundle_type=ISI_KAMAR_KOS&budget_usd=0', 
    'Budget Nol'
);

// Test Case 2: Tipe Bundle Hilang (Input harus ada)
run_test(
    $endpoint . '?budget_usd=100', 
    'Tipe Bundle Hilang'
);

// Test Case 3: Tipe Bundle Tidak Dikenali (Bukan dari Lookup Table)
run_test(
    $endpoint . '?bundle_type=NOT_FOUND&budget_usd=100', 
    'Tipe Bundle Invalid'
);

echo "\n--- Semua Input Validation Tests Berhasil Dijalankan ---\n";
exit(0);
?>