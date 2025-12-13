<?php
require_once 'config.php';

// 1. Ambil Input dari User
$bundleType = isset($_GET['bundle_type']) ? strtoupper($_GET['bundle_type']) : null;
$budgetUSD = isset($_GET['budget_usd']) ? (float)$_GET['budget_usd'] : 0;

// Validasi input
if (!isset(BUNDLE_CATEGORIES[$bundleType]) || $budgetUSD <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Input tidak valid.']);
    exit;
}

// 2. Integrasi Currency API (Panggilan cURL Pertama)
$currencyKey = get_api_key('CURRENCY_API_KEY');
$urlCurrency = CURRENCY_API_URL . $currencyKey . '&currencies=IDR';
$currencyResponse = file_get_contents($urlCurrency);

$rateIDR = 0;
if ($currencyResponse) {
    $data = json_decode($currencyResponse, true);
    $rateIDR = $data['data']['IDR'] ?? 0;
}

if ($rateIDR === 0) {
    // Error handling krusial
    echo json_encode(['status' => 'error', 'message' => 'Gagal mendapatkan nilai tukar mata uang.']);
    exit;
}

// 3. Logika Budgeting & Multi-Call eBay
$ebayAppId = get_api_key('EBAY_APP_ID');
$itemsToSearch = BUNDLE_CATEGORIES[$bundleType];
$currentBudget = $budgetUSD;
$purchasedItems = [];
$totalPurchasedUSD = 0;

// Urutkan item berdasarkan prioritas (1, 2, 3)
usort($itemsToSearch, function($a, $b) {
    return $a['priority'] <=> $b['priority'];
});

foreach ($itemsToSearch as $item) {
    $query = urlencode($item['query']);
    $urlEbay = EBAY_API_URL . $ebayAppId . '&keywords=' . $query . '&outputSelector=AspectHistogram';

    // Panggilan cURL ke eBay (Sederhana menggunakan file_get_contents untuk PHP dasar)
    $ebayResponse = file_get_contents($urlEbay);
    $ebayData = $ebayResponse ? json_decode($ebayResponse, true) : null;

    $foundItem = null;
    $itemPrice = 0;

    // --- Logika Pemilihan Item Termurah ---
    // Logika di sini akan sangat tergantung pada struktur respon eBay API (perlu disesuaikan)
    // Asumsi sederhana: Ambil item pertama dari hasil yang harganya tersedia
    if ($ebayData && isset($ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'])) {
        $bestItem = $ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'][0];
        $itemPrice = $bestItem['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 0;
        
        if ($itemPrice > 0 && $itemPrice <= $currentBudget) {
            $foundItem = $bestItem;
        }
    }
    
    // 4. Implementasi Logika Pembelian
    $status = 'Diabaikan';
    $reason = 'Melebihi budget atau item tidak ditemukan';
    
    if ($foundItem) {
        $currentBudget -= $itemPrice;
        $totalPurchasedUSD += $itemPrice;
        $status = 'Dibeli';
        $reason = 'Sesuai budget (' . round($currentBudget, 2) . ' USD sisa)';
    }

    // Tambahkan item ke daftar output
    $purchasedItems[] = [
        'item_name' => $item['item'],
        'query_used' => $item['query'],
        'price_usd' => $itemPrice,
        'price_idr' => round($itemPrice * $rateIDR, 0),
        'link_ebay' => $foundItem['viewItemURL'][0] ?? '#',
        'status_purchase' => $status,
        'reason' => $reason
    ];
}

// 5. Output Akhir
$finalResponse = [
    "status" => "success",
    "bundle_type" => $bundleType,
    "budget_input_usd" => $budgetUSD,
    "currency_rate_idr" => $rateIDR,
    "total_purchased_usd" => round($totalPurchasedUSD, 2),
    "total_purchased_idr" => round($totalPurchasedUSD * $rateIDR, 0),
    "bundle_items" => $purchasedItems
];

echo json_encode($finalResponse, JSON_PRETTY_PRINT);
?>