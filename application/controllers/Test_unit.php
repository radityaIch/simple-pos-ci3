<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unit Test Controller for Simple POS CI3
 * Uses CodeIgniter's built-in Unit Testing library
 * 
 * Access via: http://localhost:8080/test_unit/menu
 */
class Test_unit extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Load required libraries and models
        $this->load->library('unit_test');
        $this->load->model('Promo_model');
        $this->load->model('Penjualan_model');
        $this->load->model('Master_barang_model');
        
        // Load detailed test functions
        require_once APPPATH . '../tests/promo_tests.php';
        require_once APPPATH . '../tests/penjualan_tests.php';
        
        // Set up test data
        $this->_setup_test_data();
    }

    /**
     * Main test runner - runs all tests
     */
    public function index()
    {
        echo "<h1>Simple POS CI3 - Unit Test Suite</h1>";
        echo "<p>Using CodeIgniter's built-in Unit Testing library</p>";
        echo "<hr>";
        
        // Run all test suites
        $this->test_promo_model();
        $this->test_penjualan_model();
        $this->test_master_barang_model();
        $this->test_transaction_calculations();
        
        // Display results
        echo "<h2>Test Results Summary</h2>";
        echo $this->unit->report();
    }

    /**
     * Test Promo Model functionality
     */
    public function test_promo_model()
    {
        echo "<h2>Testing Promo Model (Refactored Functionality)</h2>";
        
        // Test 1: validate_promo method with valid promo
        $result = $this->Promo_model->validate_promo('DISC10');
        $this->unit->run($result['success'], TRUE, 'Promo validation should succeed for valid promo');
        $this->unit->run(isset($result['promo_info']), TRUE, 'Promo validation should return promo_info');
        
        // Test 2: validate_promo method with invalid promo
        $result = $this->Promo_model->validate_promo('INVALID');
        $this->unit->run(isset($result['error']), TRUE, 'Promo validation should return error for invalid promo');
        $this->unit->run($result['error'], 'Kode promo tidak valid', 'Error message should be correct');
        
        // Test 3: calculate_discount method returns zero discount (deprecated)
        $result = $this->Promo_model->calculate_discount('DISC10', 1000000);
        $this->unit->run($result['success'], TRUE, 'Calculate discount should succeed');
        $this->unit->run($result['discount'], 0, 'Calculate discount should return 0 (promo is label only)');
        $this->unit->run(isset($result['promo_info']), TRUE, 'Calculate discount should return promo_info');
        
        // Test 4: get_by_kode method
        $promo = $this->Promo_model->get_by_kode('DISC10');
        $this->unit->run(is_object($promo), TRUE, 'get_by_kode should return object for valid promo');
        $this->unit->run($promo->kode_promo, 'DISC10', 'Promo code should match');
        
        // Test 5: get_by_kode with non-existent promo
        $promo = $this->Promo_model->get_by_kode('NONEXISTENT');
        $this->unit->run($promo, NULL, 'get_by_kode should return NULL for non-existent promo');
        
        echo "<p><strong>Promo Model Tests Completed</strong></p>";
    }

    /**
     * Test Penjualan Model functionality
     */
    public function test_penjualan_model()
    {
        echo "<h2>Testing Penjualan Model (Transaction Calculations)</h2>";
        
        // Test 1: Basic calculation with single item
        $items = array(
            array(
                'kode_barang' => 'BRG001',
                'qty' => 2,
                'harga' => 100000,
                'discount' => 10000
            )
        );
        
        $result = $this->Penjualan_model->calculate_totals($items);
        $this->unit->run($result['success'], TRUE, 'Calculate totals should succeed');
        $this->unit->run($result['total_bayar'], 190000, 'Total should be (2*100000)-10000 = 190000');
        $this->unit->run($result['ppn'], 19000, 'PPN should be 190000 * 0.1 = 19000');
        $this->unit->run($result['discount_promo'], 0, 'Promo discount should be 0');
        $this->unit->run($result['grand_total'], 209000, 'Grand total should be 190000+19000 = 209000');
        
        // Test 2: Calculation with valid promo (as label only)
        $result = $this->Penjualan_model->calculate_totals($items, 'DISC10');
        $this->unit->run($result['success'], TRUE, 'Calculate totals with promo should succeed');
        $this->unit->run($result['discount_promo'], 0, 'Promo discount should be 0 (promo is label only)');
        $this->unit->run(is_object($result['promo_info']), TRUE, 'Promo info should be returned');
        $this->unit->run($result['promo_info']->kode_promo, 'DISC10', 'Promo code should match');
        
        // Test 3: Calculation with invalid promo
        $result = $this->Penjualan_model->calculate_totals($items, 'INVALID');
        $this->unit->run(isset($result['error']), TRUE, 'Invalid promo should return error');
        $this->unit->run($result['error'], 'Kode promo tidak valid', 'Error message should be correct');
        
        // Test 4: Multiple items calculation
        $items_multi = array(
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
        
        $result = $this->Penjualan_model->calculate_totals($items_multi);
        $expected_total = (1*100000 - 5000) + (2*200000 - 20000); // 95000 + 380000 = 475000
        $this->unit->run($result['total_bayar'], $expected_total, 'Multi-item total should be calculated correctly');
        
        // Test 5: Transaction number generation
        $transaction_no = $this->Penjualan_model->generate_transaction_number();
        $expected_prefix = 'TRX' . date('Ymd');
        $this->unit->run(strpos($transaction_no, $expected_prefix), 0, 'Transaction number should have correct prefix');
        $this->unit->run(strlen($transaction_no), 14, 'Transaction number should be 14 characters long');
        
        echo "<p><strong>Penjualan Model Tests Completed</strong></p>";
    }

    /**
     * Test Master Barang Model functionality
     */
    public function test_master_barang_model()
    {
        echo "<h2>Testing Master Barang Model (CRUD Operations)</h2>";
        
        // Test 1: Get all products
        $products = $this->Master_barang_model->get_all();
        $this->unit->run(is_array($products), TRUE, 'get_all should return array');
        $this->unit->run(count($products) > 0, TRUE, 'Should have test products');
        
        // Test 2: Get product by code
        $product = $this->Master_barang_model->get_by_kode('BRG001');
        $this->unit->run(is_object($product), TRUE, 'get_by_kode should return object');
        $this->unit->run($product->kode_barang, 'BRG001', 'Product code should match');
        
        // Test 3: Get non-existent product
        $product = $this->Master_barang_model->get_by_kode('NONEXISTENT');
        $this->unit->run($product, NULL, 'get_by_kode should return NULL for non-existent product');
        
        // Test 4: Validation tests
        $invalid_data = array(
            'kode_barang' => '',
            'nama_barang' => 'Test Product',
            'harga' => 100000
        );
        $errors = $this->Master_barang_model->validate($invalid_data);
        $this->unit->run(in_array('Kode barang harus diisi', $errors), TRUE, 'Validation should catch empty kode_barang');
        
        $invalid_data = array(
            'kode_barang' => 'TEST',
            'nama_barang' => '',
            'harga' => 100000
        );
        $errors = $this->Master_barang_model->validate($invalid_data);
        $this->unit->run(in_array('Nama barang harus diisi', $errors), TRUE, 'Validation should catch empty nama_barang');
        
        echo "<p><strong>Master Barang Model Tests Completed</strong></p>";
    }

    /**
     * Test comprehensive transaction calculations
     */
    public function test_transaction_calculations()
    {
        echo "<h2>Testing Transaction Calculations (Item Discounts)</h2>";
        
        // Test 1: No discounts
        $items = array(
            array(
                'kode_barang' => 'BRG001',
                'qty' => 2,
                'harga' => 100000,
                'discount' => 0
            )
        );
        
        $result = $this->Penjualan_model->calculate_totals($items);
        $this->unit->run($result['total_bayar'], 200000, 'Total without discount should be 2*100000 = 200000');
        $this->unit->run($result['grand_total'], 220000, 'Grand total should be 200000 + 20000 PPN = 220000');
        
        // Test 2: Large item discount
        $items = array(
            array(
                'kode_barang' => 'BRG001',
                'qty' => 3,
                'harga' => 100000,
                'discount' => 250000 // Larger than item total
            )
        );
        
        $result = $this->Penjualan_model->calculate_totals($items);
        $expected = (3 * 100000) - 250000; // 50000
        $this->unit->run($result['total_bayar'], $expected, 'Should handle discount larger than individual item total');
        
        // Test 3: Complex scenario with promo and item discounts
        $items = array(
            array(
                'kode_barang' => 'BRG001',
                'qty' => 1,
                'harga' => 100000,
                'discount' => 10000
            ),
            array(
                'kode_barang' => 'BRG002',
                'qty' => 2,
                'harga' => 200000,
                'discount' => 50000
            )
        );
        
        $result = $this->Penjualan_model->calculate_totals($items, 'DISC10');
        $expected_total = (1*100000 - 10000) + (2*200000 - 50000); // 90000 + 350000 = 440000
        $this->unit->run($result['total_bayar'], $expected_total, 'Complex calculation should be correct');
        $this->unit->run($result['discount_promo'], 0, 'Promo discount should still be 0');
        $this->unit->run(is_object($result['promo_info']), TRUE, 'Promo info should be present');
        
        // Test 4: Zero quantity
        $items = array(
            array(
                'kode_barang' => 'BRG001',
                'qty' => 0,
                'harga' => 100000,
                'discount' => 10000
            )
        );
        
        $result = $this->Penjualan_model->calculate_totals($items);
        $expected = (0 * 100000) - 10000; // -10000
        $this->unit->run($result['total_bayar'], $expected, 'Should handle zero quantity correctly');
        
        echo "<p><strong>Transaction Calculation Tests Completed</strong></p>";
    }

    /**
     * Setup test data in database
     */
    private function _setup_test_data()
    {
        // Note: In a real scenario, you might want to use a test database
        // For now, we'll work with existing data and assume test data exists
        
        // Check if test promo exists, if not create it
        $promo = $this->Promo_model->get_by_kode('DISC10');
        if (!$promo) {
            $this->db->insert('promo', array(
                'kode_promo' => 'DISC10',
                'nama_promo' => 'Test Discount 10%',
                'ketereangan' => 'Test promo for unit testing'
            ));
        }
        
        // Check if test products exist
        $product1 = $this->Master_barang_model->get_by_kode('BRG001');
        if (!$product1) {
            $this->db->insert('master_barang', array(
                'kode_barang' => 'BRG001',
                'nama_barang' => 'Test Product 1',
                'harga' => 100000
            ));
        }
        
        $product2 = $this->Master_barang_model->get_by_kode('BRG002');
        if (!$product2) {
            $this->db->insert('master_barang', array(
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Test Product 2',
                'harga' => 200000
            ));
        }
    }

    /**
     * Clean up test data
     */
    public function cleanup()
    {
        echo "<h2>Cleaning up test data...</h2>";
        
        // Remove test data (optional)
        $this->db->where('kode_promo', 'DISC10');
        $this->db->delete('promo');
        
        $this->db->where_in('kode_barang', array('BRG001', 'BRG002'));
        $this->db->delete('master_barang');
        
        echo "<p>Test data cleaned up.</p>";
        echo "<p><a href='" . base_url('test_unit') . "'>Run Tests Again</a></p>";
    }

    /**
     * Run only promo tests
     */
    public function promo()
    {
        echo "<h1>Promo Model Tests Only</h1>";
        $this->test_promo_model();
        echo $this->unit->report();
    }

    /**
     * Run only transaction tests
     */
    public function transaction()
    {
        echo "<h1>Transaction Calculation Tests Only</h1>";
        $this->test_penjualan_model();
        $this->test_transaction_calculations();
        echo $this->unit->report();
    }

    /**
     * Run detailed promo tests using external test functions
     */
    public function detailed_promo()
    {
        echo "<h1>Detailed Promo Functionality Tests</h1>";
        echo "<p>Testing refactored promo functionality where promo codes serve as labels only.</p>";
        echo "<hr>";
        
        $tests = run_all_promo_tests();
        display_promo_test_results($tests);
        
        echo "<hr>";
        echo "<p><a href='" . base_url('test_unit') . "'>← Back to All Tests</a></p>";
    }

    /**
     * Run detailed transaction tests using external test functions
     */
    public function detailed_transaction()
    {
        echo "<h1>Detailed Transaction Calculation Tests</h1>";
        echo "<p>Testing transaction calculations with item-level discounts.</p>";
        echo "<hr>";
        
        $tests = run_all_penjualan_tests();
        display_penjualan_test_results($tests);
        
        echo "<hr>";
        echo "<p><a href='" . base_url('test_unit') . "'>← Back to All Tests</a></p>";
    }

    /**
     * Run all detailed tests
     */
    public function detailed_all()
    {
        echo "<h1>Complete Detailed Test Suite</h1>";
        echo "<p>Running comprehensive tests for all refactored functionality.</p>";
        echo "<hr>";
        
        // Run detailed promo tests
        echo "<h2>Promo Functionality Tests</h2>";
        $promo_tests = run_all_promo_tests();
        display_promo_test_results($promo_tests);
        
        echo "<hr>";
        
        // Run detailed transaction tests
        echo "<h2>Transaction Calculation Tests</h2>";
        $transaction_tests = run_all_penjualan_tests();
        display_penjualan_test_results($transaction_tests);
        
        echo "<hr>";
        
        // Summary
        $total_promo = count($promo_tests);
        $total_transaction = count($transaction_tests);
        $total_tests = $total_promo + $total_transaction;
        
        $promo_passed = count(array_filter($promo_tests, function($test) {
            return $test['test'] === $test['expected'];
        }));
        
        $transaction_passed = count(array_filter($transaction_tests, function($test) {
            return $test['test'] === $test['expected'];
        }));
        
        $total_passed = $promo_passed + $transaction_passed;
        $total_failed = $total_tests - $total_passed;
        
        echo "<h2>Overall Test Summary</h2>";
        echo "<div style='padding: 15px; border: 2px solid #333; background-color: #f8f9fa;'>";
        echo "<h3>Results:</h3>";
        echo "<ul>";
        echo "<li><strong>Promo Tests:</strong> $promo_passed / $total_promo passed</li>";
        echo "<li><strong>Transaction Tests:</strong> $transaction_passed / $total_transaction passed</li>";
        echo "<li><strong>Total:</strong> $total_passed / $total_tests passed</li>";
        echo "</ul>";
        
        if ($total_failed === 0) {
            echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 ALL TESTS PASSED! 🎉</p>";
            echo "<p>The refactored promo functionality is working correctly:</p>";
            echo "<ul>";
            echo "<li>✅ Promo codes work as labels only</li>";
            echo "<li>✅ No discount calculation in promo logic</li>";
            echo "<li>✅ Item discounts handled properly</li>";
            echo "<li>✅ Transaction calculations are correct</li>";
            echo "</ul>";
        } else {
            echo "<p style='color: red; font-weight: bold; font-size: 18px;'>❌ $total_failed TESTS FAILED</p>";
            echo "<p>Please review the failing tests and check the implementation.</p>";
        }
        echo "</div>";
        
        echo "<p><a href='" . base_url('test_unit') . "'>← Back to Main Test Menu</a></p>";
    }

    /**
     * Test menu - provides links to different test suites
     */
    public function menu()
    {
        echo "<h1>Simple POS CI3 - Test Suite Menu</h1>";
        echo "<p>Choose which tests to run:</p>";
        echo "<hr>";
        
        echo "<h2>Quick Tests (CodeIgniter Unit Test Library)</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('test_unit') . "'>All Quick Tests</a> - Basic test suite using CI Unit Test</li>";
        echo "<li><a href='" . base_url('test_unit/promo') . "'>Promo Tests Only</a> - Quick promo functionality tests</li>";
        echo "<li><a href='" . base_url('test_unit/transaction') . "'>Transaction Tests Only</a> - Quick transaction tests</li>";
        echo "</ul>";
        
        echo "<h2>Detailed Tests (Comprehensive)</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('test_unit/detailed_all') . "'><strong>All Detailed Tests</strong></a> - Complete comprehensive test suite</li>";
        echo "<li><a href='" . base_url('test_unit/detailed_promo') . "'>Detailed Promo Tests</a> - Comprehensive promo functionality tests</li>";
        echo "<li><a href='" . base_url('test_unit/detailed_transaction') . "'>Detailed Transaction Tests</a> - Comprehensive transaction calculation tests</li>";
        echo "</ul>";
        
        echo "<h2>Utilities</h2>";
        echo "<ul>";
        echo "<li><a href='" . base_url('test_unit/cleanup') . "'>Clean Up Test Data</a> - Remove test data from database</li>";
        echo "</ul>";
        
        echo "<hr>";
        echo "<h3>About the Refactored Functionality</h3>";
        echo "<p>This test suite validates the refactored promo functionality where:</p>";
        echo "<ul>";
        echo "<li><strong>Promo codes</strong> serve as labels only (no discount calculation)</li>";
        echo "<li><strong>Item discounts</strong> are handled in penjualan_header_detail.discount field</li>";
        echo "<li><strong>Transaction totals</strong> = Subtotal + PPN (no promo discount deduction)</li>";
        echo "</ul>";
    }
}