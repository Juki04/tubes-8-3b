<?php
// =======================================================
// CHECK_BUNDLE_ITEMS.PHP
// Mengecek setiap item di BUNDLE_CATEGORIES apakah ditemukan
// di eBay (sandbox) menggunakan App ID yang ada di config.php.
// Jika item tidak ditemukan, script mengumpulkan saran hasil
// pencarian berdasarkan nama kategori (bundle name).
// =======================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$ebayAppId = get_api_key('EBAY_APP_ID');
if (empty($ebayAppId)) {
    echo json_encode(['error' => 'EBAY_APP_ID not configured in config.php'], JSON_PRETTY_PRINT);
    exit(1);
}

// Configuration for checks
$perItemEntries = 3; // how many items to retrieve per query when checking
$categorySuggestionEntries = 5; // suggestions when item not found

$report = [
    'checked_at' => date('c'),
    'ebay_app_id_loaded' => true,
    'categories' => []
];

foreach (BUNDLE_CATEGORIES as $categoryKey => $categoryDef) {
    $catName = $categoryDef['name'] ?? $categoryKey;
    $items = $categoryDef['items'] ?? [];

    $catReport = ['name' => $catName, 'key' => $categoryKey, 'items' => []];

    foreach ($items as $itemName => $itemCfg) {
        $query = $itemCfg['query'] ?? $itemName;

        // Build eBay Finding API request
        $params = [
            'OPERATION-NAME' => 'findItemsByKeywords',
            'SERVICE-VERSION' => '1.0.0',
            'SECURITY-APPNAME' => $ebayAppId,
            'GLOBAL-ID' => 'EBAY-US',
            'RESPONSE-DATA-FORMAT' => 'JSON',
            'keywords' => $query,
            'paginationInput.entriesPerPage' => $perItemEntries,
            'sortOrder' => 'PricePlusShippingLowest',
        ];

        $url = EBAY_API_URL_BASE . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        $itemReport = [
            'item_name' => $itemName,
            'query' => $query,
            'http_code' => $http,
            'curl_error' => $err,
            'found' => false,
            'matches' => [],
        ];

        if ($http === 200 && $resp) {
            $data = json_decode($resp, true);
            if (isset($data['findItemsByKeywordsResponse'][0]['searchResult'][0]['item']) &&
                !empty($data['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'])) {
                $itemReport['found'] = true;
                foreach ($data['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'] as $it) {
                    $title = $it['title'][0] ?? null;
                    $price = $it['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? null;
                    $view = $it['viewItemURL'][0] ?? null;
                    $itemReport['matches'][] = [
                        'title' => $title,
                        'price_usd' => $price !== null ? (float)$price : null,
                        'view_url' => $view,
                    ];
                }
            } else {
                // not found: collect suggestions by category name
                $itemReport['found'] = false;
                $suggestParams = $params;
                $suggestParams['keywords'] = $catName;
                $suggestParams['paginationInput.entriesPerPage'] = $categorySuggestionEntries;
                $suggestUrl = EBAY_API_URL_BASE . '?' . http_build_query($suggestParams);

                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $suggestUrl);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
                $sresp = curl_exec($ch2);
                $shttp = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                curl_close($ch2);

                if ($shttp === 200 && $sresp) {
                    $sdata = json_decode($sresp, true);
                    if (isset($sdata['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'])) {
                        foreach ($sdata['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'] as $sit) {
                            $title = $sit['title'][0] ?? null;
                            $price = $sit['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? null;
                            $view = $sit['viewItemURL'][0] ?? null;
                            $itemReport['matches'][] = [
                                'title' => $title,
                                'price_usd' => $price !== null ? (float)$price : null,
                                'view_url' => $view,
                            ];
                        }
                    }
                }
            }
        }

        $catReport['items'][] = $itemReport;
        // small pause to be polite to API
        usleep(200000); // 200ms
    }

    $report['categories'][] = $catReport;
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
