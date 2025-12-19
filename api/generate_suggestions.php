<?php
// Generate suggestions for items not found in check_report.json
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once 'config.php';

// Prefer executing the checker and capturing its JSON directly to avoid encoding issues
$checkerOutput = shell_exec('php ' . escapeshellarg(__DIR__ . '/check_bundle_items.php'));
if (!$checkerOutput) { echo "Failed to run check_bundle_items.php\n"; exit(1); }
$report = json_decode($checkerOutput, true);
if (!$report) { echo "invalid json from checker: " . json_last_error_msg() . "\n"; exit(1); }

$ebayAppId = get_api_key('EBAY_APP_ID');
if (empty($ebayAppId)) { echo "EBAY_APP_ID missing\n"; exit(1); }

function try_query($q, $appId, $entries=1) {
    $params = [
        'OPERATION-NAME' => 'findItemsByKeywords',
        'SERVICE-VERSION' => '1.0.0',
        'SECURITY-APPNAME' => $appId,
        'GLOBAL-ID' => 'EBAY-US',
        'RESPONSE-DATA-FORMAT' => 'JSON',
        'keywords' => $q,
        'paginationInput.entriesPerPage' => $entries,
        'sortOrder' => 'PricePlusShippingLowest',
    ];
    $url = EBAY_API_URL_BASE . '?' . http_build_query($params);
    $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); curl_setopt($ch, CURLOPT_TIMEOUT,10);
    $resp = curl_exec($ch); $http = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($http !== 200 || !$resp) return null;
    $data = json_decode($resp, true);
    if (!$data) return null;
    if (!isset($data['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'])) return null;
    $items = $data['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'];
    if (empty($items)) return null;
    $it = $items[0];
    return [
        'title' => $it['title'][0] ?? null,
        'price' => $it['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? null,
        'url' => $it['viewItemURL'][0] ?? null,
    ];
}

// stopwords to remove from queries
$stop = ['with','set','small','large','mini','portable','flexible','wireless','digital','manual','adjustable','mini','compact','smart','wifi','wireless','3-axis','3 axis','for','the','kit','budget','hemAt','hemat'];

$suggestions = ['generated_at'=>date('c'),'suggestions'=>[]];

foreach ($report['categories'] as $cat) {
    foreach ($cat['items'] as $it) {
        if (!empty($it['found'])) continue; // skip found
        $orig = $it['query'];
        $candidates = [];
        $candidates[] = $orig;
        // item name
        $candidates[] = $it['item_name'];
        // last two words of original
        $parts = preg_split('/\s+/', $orig);
        $len = count($parts);
        if ($len>=2) $candidates[] = $parts[$len-2] . ' ' . $parts[$len-1];
        if ($len>=1) $candidates[] = $parts[$len-1];
        // remove stopwords
        $filtered = array_values(array_filter(array_map(function($w) use($stop){
            return trim($w);
        }, preg_split('/\s+/', str_replace($stop, '', $orig)))));
        if (!empty($filtered)) $candidates[] = implode(' ', $filtered);
        // category name
        $candidates[] = $cat['name'];

        $best = null;
        foreach ($candidates as $q) {
            $q = trim($q);
            if (empty($q)) continue;
            $res = try_query($q, $ebayAppId, 1);
            if ($res) { $best = ['query'=>$q,'result'=>$res]; break; }
            usleep(200000);
        }
        $suggestions['suggestions'][] = [
            'category'=>$cat['key'] ?? $cat['name'],
            'item_name'=>$it['item_name'],
            'original_query'=>$orig,
            'suggested_query'=> $best? $best['query'] : null,
            'suggested_result'=>$best? $best['result'] : null,
        ];
    }
}

file_put_contents(__DIR__ . '/check_suggestions.json', json_encode($suggestions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Suggestions generated: api/check_suggestions.json\n";
