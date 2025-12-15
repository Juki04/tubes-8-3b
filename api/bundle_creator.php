<?php
// =======================================================
// BUNDLE_CREATOR.PHP - ENDPOINT UTAMA LOGIKA BISNIS
// Tim A (Backend Lead)
// =======================================================

// Pastikan skrip konfigurasi (termasuk BUNDLE_CATEGORIES dan URL) dimuat
require_once 'config.php';

// Atur header CORS untuk mengizinkan akses dari domain manapun (penting untuk development frontend)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Kunci API (diambil dari environment atau $local_keys di config.php)
$currencyKey = get_api_key('CURRENCY_API_KEY');
$ebayAppId = get_api_key('EBAY_APP_ID');

// ----------------------------------------------------
// VALIDASI DAN GUARD CLAUSES (Pencegahan Kegagalan)
// ----------------------------------------------------

// Jika kunci API krusial kosong, segera keluar (ini penting untuk CI/CD dan XAMPP)
if (empty($currencyKey) || empty($ebayAppId)) {
    http_response_code(503); // Service Unavailable
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kunci API (eBay atau Currency) belum tersedia atau aktif. Harap cek GitHub Secrets (untuk CI) atau $local_keys di config.php (untuk XAMPP).'
    ]);
    exit;
}

// ----------------------------------------------------
// 1. INPUT VALIDATION 
// ----------------------------------------------------

$bundleType = isset($_GET['bundle_type']) ? strtoupper($_GET['bundle_type']) : null;
$budgetUSD = isset($_GET['budget_usd']) ? (float)$_GET['budget_usd'] : 0.0;

// Cek Bundle Type
if (empty($bundleType) || !array_key_exists($bundleType, BUNDLE_CATEGORIES)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Bundle type tidak valid.']);
    exit;
}

// Cek Budget
if ($budgetUSD <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Budget harus lebih besar dari 0 USD.']);
    exit;
}

// Ambil definisi bundle yang diminta
$targetBundle = BUNDLE_CATEGORIES[$bundleType]; // Baris ini harus ada sebelum loop.

// ----------------------------------------------------
// 2. MULTI-API CALLS (API Currency - Mendapatkan Nilai Tukar)
// ----------------------------------------------------

$currencyRateIDR = 1.0; // Default jika API gagal
$currencyApiUrl = CURRENCY_API_URL; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $currencyApiUrl); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$currencyResponse = curl_exec($ch);
curl_close($ch);

$currencyData = json_decode($currencyResponse, true);

if (isset($currencyData['rates']['IDR'])) {
    $currencyRateIDR = $currencyData['rates']['IDR'];
}

// ----------------------------------------------------
// 3. LOGIKA BUDGETING & API EBAY
// ----------------------------------------------------

$currentBudget = $budgetUSD;
$purchasedItems = [];
$ignoredItems = [];

// Loop ini berjalan melalui items yang didefinisikan di $targetBundle
foreach ($targetBundle['items'] as $itemName => $itemConfig) {
    // --- Langkah 3a: PANGGIL EBAY FINDING API ---
    $ebayApiUrl = EBAY_API_URL_BASE; 
    $ebayQuery = http_build_query([
        'OPERATION-NAME' => 'findItemsByKeywords',
        'SERVICE-VERSION' => '1.0.0',
        'SECURITY-APPNAME' => $ebayAppId, // App ID Anda (diambil dari get_api_key)
        'GLOBAL-ID' => 'EBAY-US', 
        'RESPONSE-DATA-FORMAT' => 'JSON',
        'keywords' => $itemConfig['query'],
        'paginationInput.entriesPerPage' => 1, 
        'sortOrder' => 'PricePlusShippingLowest', 
    ]);

    $itemPriceUSD = $itemConfig['max_price_usd']; // Default ke harga termahal jika API gagal/disimulasikan
    
    // Panggilan cURL ke eBay
    $chEbay = curl_init();
    curl_setopt($chEbay, CURLOPT_URL, $ebayApiUrl . "?" . $ebayQuery); 
    curl_setopt($chEbay, CURLOPT_RETURNTRANSFER, 1);
    $ebayResponse = curl_exec($chEbay);
    curl_close($chEbay);
    
    $ebayData = json_decode($ebayResponse, true);

    // --- Ekstraksi Harga dari Respons eBay ---
    if (isset($ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'][0]['sellingStatus'][0]['currentPrice'][0]['__value__'])) {
        $foundPrice = (float)$ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'][0]['sellingStatus'][0]['currentPrice'][0]['__value__'];
        
        if ($foundPrice >= $itemConfig['min_price_usd'] && $foundPrice <= $itemConfig['max_price_usd']) {
             $itemPriceUSD = $foundPrice;
        } else {
             $itemPriceUSD = $itemConfig['max_price_usd'];
        }
    }

    // --- Langkah 3b: LOGIKA BUDGETING ---
    if ($currentBudget >= $itemPriceUSD) {
        $currentBudget -= $itemPriceUSD;
        $purchasedItems[] = [
            'name' => $itemName,
            'price_usd' => $itemPriceUSD,
            'price_idr' => round($itemPriceUSD * $currencyRateIDR, 2),
            'status_purchase' => 'Dibeli',
        ];
    } else {
        $ignoredItems[] = [
            'name' => $itemName,
            'price_usd' => $itemPriceUSD,
            'price_idr' => round($itemPriceUSD * $currencyRateIDR, 2),
            'status_purchase' => 'Diabaikan',
        ];
    }
}

// ----------------------------------------------------
// 4. GENERATE FINAL RESPONSE
// ----------------------------------------------------

$totalSpentUSD = $budgetUSD - $currentBudget;
$totalSpentIDR = $totalSpentUSD * $currencyRateIDR;

$finalResponse = [
    'status' => 'success',
    'bundle_name' => $targetBundle['name'],
    'initial_budget_usd' => $budgetUSD,
    'currency_rate_idr' => $currencyRateIDR,
    'total_spent_usd' => round($totalSpentUSD, 2),
    'total_spent_idr' => round($totalSpentIDR, 2),
    'remaining_budget_usd' => round($currentBudget, 2),
    'items_purchased' => $purchasedItems,
    'items_ignored' => $ignoredItems,
];

// Output JSON ke Frontend
echo json_encode($finalResponse, JSON_PRETTY_PRINT);
?>