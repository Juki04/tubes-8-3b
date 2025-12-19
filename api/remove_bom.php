<?php
$path = __DIR__ . '/check_report.json';
if (!file_exists($path)) { echo "no file\n"; exit(1); }
$bin = file_get_contents($path);
if (substr($bin,0,3) === "\xEF\xBB\xBF") {
    $bin = substr($bin,3);
    file_put_contents($path, $bin);
    echo "BOM removed\n";
} else {
    echo "no BOM\n";
}
?>