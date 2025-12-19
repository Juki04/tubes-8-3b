<?php
$s = file_get_contents(__DIR__ . '/check_report.json');
if ($s === false) { echo "no file\n"; exit(1); }
echo "len=" . strlen($s) . "\n";
$d = json_decode($s, true);
echo "json_err=" . json_last_error_msg() . "\n";
var_dump(is_array($d));
echo "first100: " . substr($s,0,100) . "\n";
?>