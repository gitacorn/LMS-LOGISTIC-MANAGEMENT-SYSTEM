<?php

// Simple test to verify the exact constant values
require_once 'vendor/autoload.php';

// Define constants (same as in config/constants.php)
define('IN_TRANSIT_STATUS', 'In Transit');
define('DELIVERED_STATUS', 'Delivered');

echo "=== Goods Out Status Filter Constants Test ===\n\n";
echo "IN_TRANSIT_STATUS: '" . IN_TRANSIT_STATUS . "'\n";
echo "DELIVERED_STATUS: '" . DELIVERED_STATUS . "'\n\n";

echo "Length check:\n";
echo "IN_TRANSIT_STATUS length: " . strlen(IN_TRANSIT_STATUS) . "\n";
echo "DELIVERED_STATUS length: " . strlen(DELIVERED_STATUS) . "\n\n";

echo "Character check:\n";
echo "IN_TRANSIT_STATUS bytes: " . implode(', ', array_map('ord', str_split(IN_TRANSIT_STATUS))) . "\n";
echo "DELIVERED_STATUS bytes: " . implode(', ', array_map('ord', str_split(DELIVERED_STATUS))) . "\n\n";

echo "Trim test (with potential spaces):\n";
$with_spaces = ' ' . IN_TRANSIT_STATUS . ' ';
echo "With spaces: '" . $with_spaces . "'\n";
echo "Trimmed: '" . trim($with_spaces) . "'\n";
echo "Length before trim: " . strlen($with_spaces) . "\n";
echo "Length after trim: " . strlen(trim($with_spaces)) . "\n";

?>
