# Simple POS CI3 - Test Suite Documentation

## Overview

This test suite provides comprehensive coverage for the Simple POS CI3 system using **CodeIgniter's built-in Unit Testing library**, with special focus on the refactored promo functionality where promo codes serve as labels only, and discounts are handled at the individual item level.

## Test Structure

### 📁 Test Files

```
tests/
├── promo_tests.php              # Detailed promo functionality tests
├── penjualan_tests.php          # Detailed transaction calculation tests
application/controllers/
└── Test_unit.php                # Main test controller
```

### 🔧 Configuration Files

- `test_simple.php` - Quick verification script
- `run_tests.sh` - Unix/Linux test runner (updated for CI Unit Test)
- `run_tests.bat` - Windows test runner (updated for CI Unit Test)

## CodeIgniter Unit Testing Library

This test suite uses CodeIgniter's built-in `CI_Unit_test` library as documented in the [CodeIgniter 3 User Guide](https://codeigniter.com/userguide3/libraries/unit_testing.html).

### Key Features:
- Native integration with CodeIgniter framework
- Web-based test execution and reporting
- Simple assertion methods
- HTML output with pass/fail indicators

## Test Categories

### 1. Promo Model Tests (`PromoModelTest.php`)

**Purpose**: Validates the refactored promo functionality where promos serve as labels only.

**Key Test Cases**:
- ✅ `testValidatePromoValidatesExistingPromo()` - Validates promo codes exist
- ✅ `testCalculateDiscountReturnsZeroDiscount()` - Ensures no discount calculation
- ✅ `testInsertCreatesNewPromo()` - CRUD operations
- ✅ `testValidateMethodValidatesRequiredFields()` - Data validation

**Critical Validation**: Promo codes are validated but do NOT calculate discounts.

### 2. Penjualan Model Tests (`PenjualanModelTest.php`)

**Purpose**: Tests transaction processing and calculation logic.

**Key Test Cases**:
- ✅ `testCalculateTotalsWithSingleItem()` - Basic calculation
- ✅ `testCalculateTotalsWithValidPromo()` - Promo as label only
- ✅ `testCalculateTotalsWithInvalidPromo()` - Error handling
- ✅ `testGenerateTransactionNumber()` - Transaction ID generation

**Critical Validation**: Transaction totals = Subtotal + PPN (no promo discount).

### 3. Master Barang Model Tests (`MasterBarangModelTest.php`)

**Purpose**: Tests product management CRUD operations.

**Key Test Cases**:
- ✅ `testGetAllReturnsAllProducts()` - Data retrieval
- ✅ `testInsertCreatesNewProduct()` - Data creation
- ✅ `testUpdateUpdatesExistingProduct()` - Data modification
- ✅ `testValidateMethodValidatesRequiredFields()` - Data validation

### 4. Sales Controller Tests (`SalesControllerTest.php`)

**Purpose**: Integration tests for the sales controller.

**Key Test Cases**:
- ✅ `testCalculateMethodWithValidItems()` - API endpoint testing
- ✅ `testCalculateMethodWithInvalidPromo()` - Error scenarios
- ✅ `testTransactionCreationWorkflow()` - End-to-end workflow

### 5. Transaction Calculation Tests (`TransactionCalculationTest.php`)

**Purpose**: Comprehensive testing of various discount and calculation scenarios.

**Key Test Cases**:
- ✅ `testTransactionWithNoDiscounts()` - Base case
- ✅ `testTransactionWithItemDiscountsOnly()` - Item-level discounts
- ✅ `testTransactionWithPromoAndItemDiscounts()` - Combined scenarios
- ✅ `testTransactionWithLargeItemDiscounts()` - Edge cases
- ✅ `testComplexScenarioWithMultipleItemsAndDiscounts()` - Real-world scenarios

## Key Business Logic Validation

### ✅ Refactored Promo Functionality

**Before Refactoring**:
```php
// Old: Complex discount calculation in Promo_model
$discount = $total_bayar * 0.1; // 10% discount
```

**After Refactoring**:
```php
// New: Promo is just a label, returns 0 discount
$discount = 0; // No promo discount calculation
```

**Test Validation**:
- `PromoModelTest::testCalculateDiscountReturnsZeroDiscount()` - Ensures 0 discount
- `PenjualanModelTest::testCalculateTotalsWithValidPromo()` - Validates promo as label
- `TransactionCalculationTest::testTransactionWithPromoAndItemDiscounts()` - Comprehensive scenario

### ✅ Item-Level Discount Calculation

**Formula**: `Subtotal = (Quantity × Price) - Item Discount`

**Test Validation**:
- Multiple test cases verify individual item discount calculations
- Edge cases test scenarios where discounts > item totals
- Complex scenarios test multiple items with varying discounts

### ✅ Transaction Total Calculation

**Formula**: `Grand Total = Total + PPN` (No promo discount)

**Components**:
1. **Item Subtotal**: `(Qty × Price) - Item Discount`
2. **Total**: Sum of all item subtotals
3. **PPN**: `Total × 10%`
4. **Grand Total**: `Total + PPN`

## Running Tests

### Access Methods

Since we're using CodeIgniter's Unit Testing library, tests are executed via web browser:

#### Option 1: Test Menu (Recommended)

**Main Test Menu**:
```
http://localhost/simple-pos-ci3/index.php/test_unit/menu
```

Provides links to all available test suites:
- Quick Tests (basic CI Unit Test functionality)
- Detailed Tests (comprehensive validation)
- Individual test categories

#### Option 2: Direct Test Execution

**All Tests**:
```
http://localhost/simple-pos-ci3/index.php/test_unit
```

**Detailed Comprehensive Tests**:
```
http://localhost/simple-pos-ci3/index.php/test_unit/detailed_all
```

**Promo Tests Only**:
```
http://localhost/simple-pos-ci3/index.php/test_unit/detailed_promo
```

**Transaction Tests Only**:
```
http://localhost/simple-pos-ci3/index.php/test_unit/detailed_transaction
```

#### Option 3: Quick Verification

**Command Line Verification**:
```bash
php test_simple.php
```

Runs a quick verification of the refactored functionality.

## Test Results Interpretation

### ✅ Success Indicators

- All tests pass (green output)
- Coverage report generated in `tests/coverage/`
- No fatal errors or exceptions

### ❌ Failure Indicators

- Red test output with specific failure details
- Error messages indicating broken functionality
- Missing dependencies or configuration issues

## Coverage Goals

- **Models**: 90%+ code coverage
- **Controllers**: 80%+ code coverage
- **Business Logic**: 100% critical path coverage

## Continuous Integration

These tests are designed to be run in CI/CD pipelines. The test suite:

- Uses in-memory SQLite for fast execution
- Includes comprehensive error handling
- Provides detailed output for debugging
- Generates coverage reports

## Maintenance Notes

### When Adding New Features

1. Add corresponding test cases
2. Update existing tests if business logic changes
3. Maintain test documentation
4. Ensure coverage thresholds are met

### When Modifying Promo Logic

**Important**: The promo functionality has been refactored to serve as labels only. Any changes to promo logic should:

1. Maintain the "label only" principle
2. Update `PromoModelTest.php` accordingly
3. Verify transaction calculations remain correct
4. Document any changes in this file

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Solution: Check SQLite is available and writable

2. **PHPUnit Not Found**
   - Solution: Run `composer install` or add PHPUnit to PATH

3. **Test Failures After Refactoring**
   - Solution: Review business logic changes and update tests accordingly

### Getting Help

For test-related issues:
1. Check the test output for specific error messages
2. Review the test documentation above
3. Examine the test code for expected vs actual behavior
4. Ensure the development environment matches test requirements