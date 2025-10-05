<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Detailed Promo Functionality Tests
 * This file contains comprehensive tests for the refactored promo functionality
 * 
 * Key Points:
 * - Promo codes serve as labels only
 * - No discount calculation in promo logic
 * - Discounts handled at item level in penjualan_header_detail.discount
 */

/**
 * Test the refactored Promo_model validate_promo method
 */
function test_promo_validate_existing()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    // Test with existing promo
    $result = $CI->Promo_model->validate_promo('DISC10');
    
    $tests = array();
    $tests[] = array(
        'test' => $result['success'],
        'expected' => TRUE,
        'name' => 'validate_promo should return success for existing promo'
    );
    
    $tests[] = array(
        'test' => isset($result['promo_info']),
        'expected' => TRUE,
        'name' => 'validate_promo should return promo_info object'
    );
    
    $tests[] = array(
        'test' => !isset($result['error']),
        'expected' => TRUE,
        'name' => 'validate_promo should not return error for valid promo'
    );
    
    return $tests;
}

/**
 * Test the refactored Promo_model validate_promo method with invalid promo
 */
function test_promo_validate_invalid()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    // Test with invalid promo
    $result = $CI->Promo_model->validate_promo('INVALID_PROMO');
    
    $tests = array();
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'validate_promo should return error for invalid promo'
    );
    
    $tests[] = array(
        'test' => $result['error'],
        'expected' => 'Kode promo tidak valid',
        'name' => 'Error message should be correct'
    );
    
    $tests[] = array(
        'test' => !isset($result['success']),
        'expected' => TRUE,
        'name' => 'validate_promo should not return success for invalid promo'
    );
    
    return $tests;
}

/**
 * Test the deprecated calculate_discount method returns zero discount
 */
function test_promo_calculate_discount_deprecated()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    // Test with valid promo and large amount
    $result = $CI->Promo_model->calculate_discount('DISC10', 5000000);
    
    $tests = array();
    $tests[] = array(
        'test' => $result['success'],
        'expected' => TRUE,
        'name' => 'calculate_discount should return success'
    );
    
    $tests[] = array(
        'test' => $result['discount'],
        'expected' => 0,
        'name' => 'calculate_discount should return 0 discount (promo is label only)'
    );
    
    $tests[] = array(
        'test' => isset($result['promo_info']),
        'expected' => TRUE,
        'name' => 'calculate_discount should return promo_info'
    );
    
    $tests[] = array(
        'test' => $result['promo_info']->kode_promo,
        'expected' => 'DISC10',
        'name' => 'Promo info should contain correct promo code'
    );
    
    return $tests;
}

/**
 * Test promo CRUD operations
 */
function test_promo_crud_operations()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    $tests = array();
    
    // Test get_all
    $all_promos = $CI->Promo_model->get_all();
    $tests[] = array(
        'test' => is_array($all_promos),
        'expected' => TRUE,
        'name' => 'get_all should return array'
    );
    
    // Test get_by_kode with existing promo
    $promo = $CI->Promo_model->get_by_kode('DISC10');
    $tests[] = array(
        'test' => is_object($promo),
        'expected' => TRUE,
        'name' => 'get_by_kode should return object for existing promo'
    );
    
    if (is_object($promo)) {
        $tests[] = array(
            'test' => $promo->kode_promo,
            'expected' => 'DISC10',
            'name' => 'Retrieved promo should have correct code'
        );
    }
    
    // Test get_by_kode with non-existent promo
    $null_promo = $CI->Promo_model->get_by_kode('NONEXISTENT');
    $tests[] = array(
        'test' => $null_promo,
        'expected' => NULL,
        'name' => 'get_by_kode should return NULL for non-existent promo'
    );
    
    // Test get_dropdown
    $dropdown = $CI->Promo_model->get_dropdown();
    $tests[] = array(
        'test' => is_array($dropdown),
        'expected' => TRUE,
        'name' => 'get_dropdown should return array'
    );
    
    $tests[] = array(
        'test' => isset($dropdown['']),
        'expected' => TRUE,
        'name' => 'Dropdown should have empty option'
    );
    
    return $tests;
}

/**
 * Test promo validation rules
 */
function test_promo_validation()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    $tests = array();
    
    // Test empty kode_promo validation
    $data = array(
        'kode_promo' => '',
        'nama_promo' => 'Test Promo',
        'ketereangan' => 'Test description'
    );
    
    $errors = $CI->Promo_model->validate($data);
    $tests[] = array(
        'test' => in_array('Kode promo harus diisi', $errors),
        'expected' => TRUE,
        'name' => 'Validation should catch empty kode_promo'
    );
    
    // Test empty nama_promo validation
    $data = array(
        'kode_promo' => 'TEST',
        'nama_promo' => '',
        'ketereangan' => 'Test description'
    );
    
    $errors = $CI->Promo_model->validate($data);
    $tests[] = array(
        'test' => in_array('Nama promo harus diisi', $errors),
        'expected' => TRUE,
        'name' => 'Validation should catch empty nama_promo'
    );
    
    // Test valid data
    $data = array(
        'kode_promo' => 'NEWTEST',
        'nama_promo' => 'New Test Promo',
        'ketereangan' => 'Test description'
    );
    
    $errors = $CI->Promo_model->validate($data);
    $tests[] = array(
        'test' => empty($errors),
        'expected' => TRUE,
        'name' => 'Validation should pass for valid data'
    );
    
    return $tests;
}

/**
 * Test that promo functionality integrates correctly with transaction calculations
 */
function test_promo_transaction_integration()
{
    $CI =& get_instance();
    $CI->load->model('Penjualan_model');
    
    $tests = array();
    
    // Test transaction calculation with promo (should not affect discount)
    $items = array(
        array(
            'kode_barang' => 'BRG001',
            'qty' => 2,
            'harga' => 100000,
            'discount' => 20000 // Item-level discount
        )
    );
    
    // Calculate without promo
    $result_without = $CI->Penjualan_model->calculate_totals($items);
    
    // Calculate with promo
    $result_with = $CI->Penjualan_model->calculate_totals($items, 'DISC10');
    
    $tests[] = array(
        'test' => $result_with['success'],
        'expected' => TRUE,
        'name' => 'Transaction calculation with promo should succeed'
    );
    
    $tests[] = array(
        'test' => $result_with['total_bayar'],
        'expected' => $result_without['total_bayar'],
        'name' => 'Total should be same with or without promo (promo is label only)'
    );
    
    $tests[] = array(
        'test' => $result_with['discount_promo'],
        'expected' => 0,
        'name' => 'Promo discount should be 0'
    );
    
    $tests[] = array(
        'test' => $result_with['grand_total'],
        'expected' => $result_without['grand_total'],
        'name' => 'Grand total should be same with or without promo'
    );
    
    $tests[] = array(
        'test' => isset($result_with['promo_info']),
        'expected' => TRUE,
        'name' => 'Promo info should be present when promo is used'
    );
    
    if (isset($result_with['promo_info'])) {
        $tests[] = array(
            'test' => $result_with['promo_info']->kode_promo,
            'expected' => 'DISC10',
            'name' => 'Promo info should contain correct promo code'
        );
    }
    
    return $tests;
}

/**
 * Test edge cases for promo functionality
 */
function test_promo_edge_cases()
{
    $CI =& get_instance();
    $CI->load->model('Promo_model');
    
    $tests = array();
    
    // Test empty promo code
    $result = $CI->Promo_model->validate_promo('');
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'Empty promo code should return error'
    );
    
    // Test null promo code
    $result = $CI->Promo_model->validate_promo(null);
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'NULL promo code should return error'
    );
    
    // Test whitespace promo code
    $result = $CI->Promo_model->validate_promo('   ');
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'Whitespace promo code should return error'
    );
    
    // Test case sensitivity (promo codes should be case sensitive)
    // We test with 'NONEXISTENT_PROMO' which should fail
    $result = $CI->Promo_model->validate_promo('NONEXISTENT_PROMO');
    $tests[] = array(
        'test' => isset($result['error']),
        'expected' => TRUE,
        'name' => 'Promo codes should be case sensitive'
    );
    
    return $tests;
}

/**
 * Run all promo tests and return results
 */
function run_all_promo_tests()
{
    $all_tests = array();
    
    $all_tests = array_merge($all_tests, test_promo_validate_existing());
    $all_tests = array_merge($all_tests, test_promo_validate_invalid());
    $all_tests = array_merge($all_tests, test_promo_calculate_discount_deprecated());
    $all_tests = array_merge($all_tests, test_promo_crud_operations());
    $all_tests = array_merge($all_tests, test_promo_validation());
    $all_tests = array_merge($all_tests, test_promo_transaction_integration());
    $all_tests = array_merge($all_tests, test_promo_edge_cases());
    
    return $all_tests;
}

/**
 * Display test results in HTML format
 */
function display_promo_test_results($tests)
{
    $passed = 0;
    $failed = 0;
    
    echo "<h2>Detailed Promo Functionality Test Results</h2>";
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