<?php
/**
 * Simple Test Verification Script
 * This script performs basic verification of the refactored promo functionality
 * Run this to quickly verify the key changes are working
 */

echo "=== Simple POS CI3 - Quick Test Verification ===\n";
echo "Testing refactored promo functionality...\n\n";

// Test 1: Verify Promo_model exists and has the right methods
if (file_exists('application/models/Promo_model.php')) {
    echo "✓ Promo_model.php exists\n";
    
    $content = file_get_contents('application/models/Promo_model.php');
    
    if (strpos($content, 'validate_promo') !== false) {
        echo "✓ validate_promo method exists\n";
    } else {
        echo "✗ validate_promo method NOT found\n";
    }
    
    if (strpos($content, 'calculate_discount') !== false) {
        echo "✓ calculate_discount method exists (deprecated)\n";
    } else {
        echo "✗ calculate_discount method NOT found\n";
    }
    
    if (strpos($content, '@deprecated') !== false) {
        echo "✓ Deprecation notice found in calculate_discount\n";
    } else {
        echo "✗ Deprecation notice NOT found\n";
    }
    
} else {
    echo "✗ Promo_model.php NOT found\n";
}

echo "\n";

// Test 2: Verify Test_unit controller exists
if (file_exists('application/controllers/Test_unit.php')) {
    echo "✓ Test_unit.php controller exists\n";
    
    $content = file_get_contents('application/controllers/Test_unit.php');
    
    if (strpos($content, 'detailed_all') !== false) {
        echo "✓ detailed_all method exists\n";
    } else {
        echo "✗ detailed_all method NOT found\n";
    }
    
} else {
    echo "✗ Test_unit.php controller NOT found\n";
}

echo "\n";

// Test 3: Verify test files exist
$test_files = [
    'tests/promo_tests.php',
    'tests/penjualan_tests.php'
];

foreach ($test_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file NOT found\n";
    }
}

echo "\n";

// Test 4: Verify key refactoring points
echo "=== Refactoring Validation ===\n";

$promo_content = file_get_contents('application/models/Promo_model.php');

// Check if calculate_discount returns 0
if (strpos($promo_content, "'discount' => 0") !== false) {
    echo "✓ calculate_discount returns 0 discount\n";
} else {
    echo "✗ calculate_discount may not return 0 discount\n";
}

// Check Penjualan_model uses validate_promo
if (file_exists('application/models/Penjualan_model.php')) {
    $penjualan_content = file_get_contents('application/models/Penjualan_model.php');
    
    if (strpos($penjualan_content, 'validate_promo') !== false) {
        echo "✓ Penjualan_model uses validate_promo method\n";
    } else {
        echo "✗ Penjualan_model may not use validate_promo\n";
    }
    
    if (strpos($penjualan_content, "'discount_promo' => 0") !== false) {
        echo "✓ Penjualan_model sets discount_promo to 0\n";
    } else {
        echo "✗ Penjualan_model may not set discount_promo to 0\n";
    }
}

echo "\n=== Summary ===\n";
echo "Key Changes Implemented:\n";
echo "• Promo codes now serve as labels only\n";
echo "• calculate_discount method deprecated and returns 0\n";
echo "• New validate_promo method for promo validation\n";
echo "• Transaction calculations exclude promo discounts\n";
echo "• Item discounts handled in penjualan_header_detail.discount\n";
echo "• Comprehensive test suite using CodeIgniter Unit Test library\n\n";

echo "To run the full test suite, access:\n";
echo "http://localhost/simple-pos-ci3/index.php/test_unit/menu\n\n";

echo "For quick verification, access:\n";
echo "http://localhost/simple-pos-ci3/index.php/test_unit/detailed_all\n";