<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Detailed Penjualan Model Tests
 * This file contains comprehensive tests for transaction calculations
 * 
 * Key Points:
 * - Item discounts are handled in penjualan_header_detail.discount
 * - Promo discount is always 0 (promo is label only)
 * - Grand total = Total + PPN (no promo discount deduction)
 */

/**
 * Test basic transaction calculation
 */
function test_basic_transaction_calculation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 2,
            'harga' => 100000,
            'discount' => 10000
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items);
    
    $tests[] = array(
        'test' => $result['success'],
        'expected' => TRUE,
        'name' => 'Basic calculation should succeed'
    );
    
    $expected_total = (2 * 100000) - 10000; // 190000
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => $expected_total,
        'name' => 'Total should be (qty * price) - discount = 190000'
    );
    
    $expected_ppn = (int)($expected_total * 0.1); // 19000
    $tests[] = array(
        'test' => $result['ppn'],
        'expected' => $expected_ppn,
        'name' => 'PPN should be 10% of total = 19000'
    );
    
    $tests[] = array(
        'test' => $result['discount_promo'],
        'expected' => 0,
        'name' => 'Promo discount should be 0'
    );
    
    $expected_grand = $expected_total + $expected_ppn; // 209000
    $tests[] = array(
        'test' => $result['grand_total'],
        'expected' => $expected_grand,
        'name' => 'Grand total should be total + PPN = 209000'
    );
    
    return $tests;
}

/**
 * Test multiple items calculation
 */
function test_multiple_items_calculation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 1,
            'harga' => 100000,
            'discount' => 5000
        ),
        array(
            'kode_barang' => 'BRG002',
            'qty' => 2,
            'harga' => 200000,
            'discount' => 20000
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items);
    
    // Item 1: (1 * 100000) - 5000 = 95000
    // Item 2: (2 * 200000) - 20000 = 380000
    // Total: 95000 + 380000 = 475000
    $expected_total = 475000;
    
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => $expected_total,
        'name' => 'Multiple items total should be calculated correctly'
    );
    
    $tests[] = array(
        'test' => count($result['items']),
        'expected' => 2,
        'name' => 'Should return correct number of calculated items'
    );
    
    $tests[] = array(
        'test' => $result['items'][0]['subtotal'],
        'expected' => 95000,
        'name' => 'First item subtotal should be correct'
    );
    
    $tests[] = array(
        'test' => $result['items'][1]['subtotal'],
        'expected' => 380000,
        'name' => 'Second item subtotal should be correct'
    );
    
    return $tests;
}

/**
 * Test transaction calculation with promo (should not affect totals)
 */
function test_transaction_with_promo()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 1,
            'harga' => 100000,
            'discount' => 0
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items, 'DISC10');
    
    $tests[] = array(
        'test' => $result['success'],
        'expected' => TRUE,
        'name' => 'Calculation with promo should succeed'
    );
    
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => 100000,
        'name' => 'Total should not be affected by promo'
    );
    
    $tests[] = array(
        'test' => $result['discount_promo'],
        'expected' => 0,
        'name' => 'Promo discount should be 0 (promo is label only)'
    );
    
    $tests[] = array(
        'test' => $result['grand_total'],
        'expected' => 110000,
        'name' => 'Grand total should be total + PPN (no promo discount)'
    );
    
    $tests[] = array(
        'test' => isset($result['promo_info']),
        'expected' => TRUE,
        'name' => 'Promo info should be present'
    );
    
    if (isset($result['promo_info'])) {
        $tests[] = array(
            'test' => $result['promo_info']->kode_promo,
            'expected' => 'DISC10',
            'name' => 'Promo info should contain correct promo code'
        );
    }
    
    return $tests;
}

/**
 * Test transaction calculation with invalid promo
 */
function test_transaction_with_invalid_promo()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 1,
            'harga' => 100000,
            'discount' => 0
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items, 'INVALID');
    
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'Invalid promo should return error'
    );
    
    $tests[] = array(
        'test' => $result['error'],
        'expected' => 'Kode promo tidak valid',
        'name' => 'Error message should be correct'
    );
    
    return $tests;
}

/**
 * Test transaction calculation with non-existent product
 */
function test_transaction_with_invalid_product()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'NONEXISTENT',
            'qty' => 1,
            'harga' => 100000,
            'discount' => 0
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items);
    
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'Non-existent product should return error'
    );
    
    $tests[] = array(
        'test' => strpos($result['error'], 'tidak ditemukan') !== FALSE,
        'expected' => TRUE,
        'name' => 'Error message should mention product not found'
    );
    
    return $tests;
}

/**
 * Test edge case: large discount
 */
function test_large_discount_calculation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 3,
            'harga' => 100000,
            'discount' => 250000 // Larger than item total
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items);
    
    $expected_total = (3 * 100000) - 250000; // 50000
    
    $tests[] = array(
        'test' => $result['success'],
        'expected' => TRUE,
        'name' => 'Large discount calculation should succeed'
    );
    
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => $expected_total,
        'name' => 'Should handle discount larger than individual item calculation'
    );
    
    $tests[] = array(
        'test' => $result['items'][0]['subtotal'],
        'expected' => $expected_total,
        'name' => 'Item subtotal should reflect large discount'
    );
    
    return $tests;
}

/**
 * Test zero quantity handling
 */
function test_zero_quantity_calculation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 0,
            'harga' => 100000,
            'discount' => 10000
        ),
        array(
            'kode_barang' => 'BRG002',
            'qty' => 2,
            'harga' => 200000,
            'discount' => 0
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items);
    
    // Item 1: (0 * 100000) - 10000 = -10000
    // Item 2: (2 * 200000) - 0 = 400000
    // Total: -10000 + 400000 = 390000
    $expected_total = 390000;
    
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => $expected_total,
        'name' => 'Zero quantity should be handled correctly'
    );
    
    $tests[] = array(
        'test' => $result['items'][0]['subtotal'],
        'expected' => -10000,
        'name' => 'Zero quantity item subtotal should be negative due to discount'
    );
    
    return $tests;
}

/**
 * Test transaction number generation
 */
function test_transaction_number_generation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $transaction_no1 = $CI->Penjualan_model->generate_transaction_number();
    sleep(3);
    $transaction_no2 = $CI->Penjualan_model->generate_transaction_number();
    
    $expected_prefix = 'TRX' . date('Ymd');
    
    $tests[] = array(
        'test' => strpos($transaction_no1, $expected_prefix),
        'expected' => 0,
        'name' => 'Transaction number should have correct prefix'
    );
    
    $tests[] = array(
        'test' => strlen($transaction_no1),
        'expected' => 14,
        'name' => 'Transaction number should be 14 characters long'
    );
    
    return $tests;
}

/**
 * Test transaction validation
 */
function test_transaction_validation()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    // Test missing customer
    $header_data = array(
        'customer' => '',
        'kode_promo' => 'DISC10'
    );
    
    $details_data = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 1,
            'harga' => 100000,
            'discount' => 0
        )
    );
    
    $errors = $CI->Penjualan_model->validate_transaction($header_data, $details_data);
    $tests[] = array(
        'test' => in_array('Nama customer harus diisi', $errors),
        'expected' => TRUE,
        'name' => 'Validation should catch missing customer'
    );
    
    // Test empty details
    $header_data['customer'] = 'Test Customer';
    $errors = $CI->Penjualan_model->validate_transaction($header_data, array());
    $tests[] = array(
        'test' => in_array('Detail transaksi harus diisi', $errors),
        'expected' => TRUE,
        'name' => 'Validation should catch empty transaction details'
    );
    
    // Test valid data
    $errors = $CI->Penjualan_model->validate_transaction($header_data, $details_data);
    $tests[] = array(
        'test' => empty($errors),
        'expected' => TRUE,
        'name' => 'Validation should pass for valid data'
    );
    
    return $tests;
}

/**
 * Test complex calculation scenario
 */
function test_complex_calculation_scenario()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 3,
            'harga' => 150000,
            'discount' => 45000 // 10% of item total
        ),
        array(
            'kode_barang' => 'BRG002',
            'qty' => 1,
            'harga' => 500000,
            'discount' => 100000 // 20% of item total
        ),
        array(
            'kode_barang' => 'BRG001', // Same product, different line
            'qty' => 2,
            'harga' => 150000,
            'discount' => 0 // No discount
        )
    );
    
    $result = $CI->Penjualan_model->calculate_totals($items, 'DISC10');
    
    // Item 1: (3 * 150000) - 45000 = 405000
    // Item 2: (1 * 500000) - 100000 = 400000
    // Item 3: (2 * 150000) - 0 = 300000
    // Total: 405000 + 400000 + 300000 = 1105000
    $expected_total = 1105000;
    
    $tests[] = array(
        'test' => $result['total_bayar'],
        'expected' => $expected_total,
        'name' => 'Complex scenario total should be calculated correctly'
    );
    
    $tests[] = array(
        'test' => count($result['items']),
        'expected' => 3,
        'name' => 'Should handle multiple line items correctly'
    );
    
    $tests[] = array(
        'test' => $result['discount_promo'],
        'expected' => 0,
        'name' => 'Promo discount should be 0 even in complex scenario'
    );
    
    $expected_ppn = (int)($expected_total * 0.1); // 110500
    $tests[] = array(
        'test' => $result['ppn'],
        'expected' => $expected_ppn,
        'name' => 'PPN should be calculated correctly on large amount'
    );
    
    $expected_grand = $expected_total + $expected_ppn; // 1215500
    $tests[] = array(
        'test' => $result['grand_total'],
        'expected' => $expected_grand,
        'name' => 'Grand total should be correct in complex scenario'
    );
    
    $tests[] = array(
        'test' => isset($result['promo_info']),
        'expected' => TRUE,
        'name' => 'Promo info should be present in complex scenario'
    );
    
    return $tests;
}

/**
 * Run all penjualan tests and return results
 */
function run_all_penjualan_tests()
{
    $all_tests = array();
    
    $all_tests = array_merge($all_tests, test_basic_transaction_calculation());
    $all_tests = array_merge($all_tests, test_multiple_items_calculation());
    $all_tests = array_merge($all_tests, test_transaction_with_promo());
    $all_tests = array_merge($all_tests, test_transaction_with_invalid_promo());
    $all_tests = array_merge($all_tests, test_transaction_with_invalid_product());
    $all_tests = array_merge($all_tests, test_large_discount_calculation());
    $all_tests = array_merge($all_tests, test_zero_quantity_calculation());
    $all_tests = array_merge($all_tests, test_transaction_number_generation());
    $all_tests = array_merge($all_tests, test_transaction_validation());
    $all_tests = array_merge($all_tests, test_complex_calculation_scenario());
    
    return $all_tests;
}

/**
 * Display test results in HTML format
 */
function display_penjualan_test_results($tests)
{
    $passed = 0;
    $failed = 0;
    
    echo "<h2>Detailed Penjualan Model Test Results</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Test Name</th><th>Expected</th><th>Actual</th><th>Result</th></tr>";
    
    foreach ($tests as $test) {
        $result = ($test['test'] === $test['expected']) ? 'PASS' : 'FAIL';
        $row_class = ($result === 'PASS') ? 'style="background-color: #d4edda;"' : 'style="background-color: #f8d7da;"';
        
        if ($result === 'PASS') {
            $passed++;
        } else {
            $failed++;
        }
        
        echo "<tr $row_class>";
        echo "<td>" . htmlspecialchars($test['name']) . "</td>";
        echo "<td>" . var_export($test['expected'], true) . "</td>";
        echo "<td>" . var_export($test['test'], true) . "</td>";
        echo "<td><strong>$result</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Summary:</strong> $passed passed, $failed failed, " . count($tests) . " total</p>";
    
    if ($failed > 0) {
        echo "<p style='color: red;'><strong>Some tests failed! Please review the implementation.</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>All tests passed! ✅</strong></p>";
    }
}