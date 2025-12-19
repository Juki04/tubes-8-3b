<?php
$data = json_decode(file_get_contents(__DIR__ . '/check_report.json'), true);
$total = 0;
$found = 0;
foreach ($data['categories'] as $cat) {
    foreach ($cat['items'] as $it) {
        $total++;
        if (!empty($it['found'])) $found++;
    }
}
echo "total_items:$total\nfound_items:$found\nmissing_items:" . ($total - $found) . "\n";
?>
