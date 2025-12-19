<?php
// =======================================================
// DEBUG_EBAY_API.PHP - TESTING eBay API RESPONSE
// File untuk debugging - TIDAK mengubah file production
// =======================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "=== eBay API Response Debugging ===\n\n";

// Ambil API keys
$ebayAppId = get_api_key('EBAY_APP_ID');
$currencyKey = get_api_key('CURRENCY_API_KEY');

echo "eBay App ID: " . ($ebayAppId ? "✓ Loaded" : "✗ NOT FOUND") . "\n";
echo "Currency Key: " . ($currencyKey ? "✓ Loaded" : "✗ NOT FOUND") . "\n\n";

if (empty($ebayAppId)) {
    echo "❌ ERROR: eBay App ID tidak ditemukan!\n";
    exit(1);
}

// Test dengan beberapa query populer untuk memastikan sandbox mengembalikan items
$testQueries = [
    'iphone',
    'laptop',
    'headphones',
    'sneakers',
    'coffee maker',
];

foreach ($testQueries as $index => $query) {
    echo "--- Test " . ($index + 1) . ": '$query' ---\n";
    
    $ebayApiUrl = EBAY_API_URL_BASE;
    $ebayParams = [
        'OPERATION-NAME' => 'findItemsByKeywords',
        'SERVICE-VERSION' => '1.0.0',
        'SECURITY-APPNAME' => $ebayAppId,
        'GLOBAL-ID' => 'EBAY-US',
        'RESPONSE-DATA-FORMAT' => 'JSON',
        'keywords' => $query,
        'paginationInput.entriesPerPage' => 5,
        'sortOrder' => 'PricePlusShippingLowest',
    ];
    
    $fullUrl = $ebayApiUrl . "?" . http_build_query($ebayParams);
    
    echo "URL: " . substr($fullUrl, 0, 100) . "...\n";
    echo "Making request...\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    
    if (!empty($curlError)) {
        echo "❌ cURL Error: $curlError\n\n";
        continue;
    }
    
    if (empty($response)) {
        echo "❌ Empty response\n\n";
        continue;
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON decode error: " . json_last_error_msg() . "\n";
        echo "Raw response: " . substr($response, 0, 200) . "...\n\n";
        continue;
    }
    
    // Debug: tampilkan struktur response
    echo "Response keys: " . implode(", ", array_keys($data)) . "\n";
    
    // Cek error dari eBay
    if (isset($data['findItemsByKeywordsResponse'][0]['ack'][0]) && 
        $data['findItemsByKeywordsResponse'][0]['ack'][0] !== 'Success') {
        echo "❌ eBay Error: " . $data['findItemsByKeywordsResponse'][0]['ack'][0] . "\n";
        if (isset($data['findItemsByKeywordsResponse'][0]['errorMessage'])) {
            echo "Message: " . print_r($data['findItemsByKeywordsResponse'][0]['errorMessage'], true);
        }
        echo "\n\n";
        continue;
    }
    
    // Cek struktur searchResult
    if (!isset($data['findItemsByKeywordsResponse'][0]['searchResult'][0])) {
        echo "❌ No searchResult found in response\n";
        echo "Full response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";
        continue;
    }
    
    $searchResult = $data['findItemsByKeywordsResponse'][0]['searchResult'][0];
    
    if (!isset($searchResult['item']) || empty($searchResult['item'])) {
        echo "❌ No items found in search result\n\n";
        continue;
    }
    
    $item = $searchResult['item'][0];
    
    // Extract data
    $title = $item['title'][0] ?? 'N/A';
    $price = $item['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 'N/A';
    $viewItemURL = $item['viewItemURL'][0] ?? 'N/A';
    $itemId = $item['itemId'][0] ?? 'N/A';
    
    echo "✓ Success!\n";
    echo "  Title: $title\n";
    echo "  Price: \$$price\n";
    echo "  Item ID: $itemId\n";
    echo "  View URL: $viewItemURL\n";
    echo "  Item keys: " . implode(", ", array_keys($item)) . "\n\n";
}

echo "\n=== End of Debug ===\n";
echo "Dokumentasi structure response tersedia di atas.\n";
echo "Jika ada item dengan viewItemURL, parsing sudah benar.\n";
?>
