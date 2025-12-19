<?php
require_once 'config.php';

$ebayAppId = get_api_key('EBAY_APP_ID');

// Test query sederhana
$ebayApiUrl = EBAY_API_URL_BASE; 
$ebayQuery = http_build_query([
    'OPERATION-NAME' => 'findItemsByKeywords',
    'SERVICE-VERSION' => '1.0.0',
    'SECURITY-APPNAME' => $ebayAppId,
    'GLOBAL-ID' => 'EBAY-US', 
    'RESPONSE-DATA-FORMAT' => 'JSON',
    'keywords' => 'gaming mouse',
    'paginationInput.entriesPerPage' => 1, 
    'sortOrder' => 'PricePlusShippingLowest', 
]);

$chEbay = curl_init();
curl_setopt($chEbay, CURLOPT_URL, $ebayApiUrl . "?" . $ebayQuery); 
curl_setopt($chEbay, CURLOPT_RETURNTRANSFER, 1);
$ebayResponse = curl_exec($chEbay);
curl_close($chEbay);

$ebayData = json_decode($ebayResponse, true);

echo "=== STRUKTUR RESPONSE eBay ===\n\n";
echo json_encode($ebayData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo "\n\n=== AKSES ITEM PERTAMA ===\n\n";
if (isset($ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'][0])) {
    $item = $ebayData['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'][0];
    
    echo "SEMUA KEY DALAM ITEM:\n";
    print_r(array_keys($item));
    
    echo "\n\nCARIAN untuk viewItemURL:\n";
    if (isset($item['viewItemURL'])) {
        echo "item['viewItemURL'] = " . print_r($item['viewItemURL'], true);
    } else {
        echo "TIDAK DITEMUKAN di item['viewItemURL']\n";
    }
    
    if (isset($item['viewItemURL'][0])) {
        echo "\nitem['viewItemURL'][0] = " . $item['viewItemURL'][0];
    }
    
} else {
    echo "ITEM TIDAK DITEMUKAN!\n";
}
?>
