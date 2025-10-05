# Test Execution Guide - Simple POS CI3

## 🚀 Quick Start

### 1. Verify Setup
```bash
php test_simple.php
```

This will check if all files are in place and the refactoring was successful.

### 2. Run Full Test Suite

**Open your web browser and navigate to:**
```
http://localhost:8080/test_unit/menu
```

**Or run all tests directly:**
```
http://localhost:8080/test_unit/detailed_all
```

## 📋 Available Test Suites

### Main Test Menu
- **URL**: `http://localhost:8080/test_unit/menu`
- **Purpose**: Interactive menu to choose which tests to run

### Comprehensive Test Suite
- **URL**: `http://localhost:8080/test_unit/detailed_all`
- **Purpose**: Run all detailed tests for complete validation
- **Recommended**: ✅ Use this for full validation

### Individual Test Categories

#### Promo Functionality Tests
- **URL**: `http://localhost:8080/test_unit/detailed_promo`
- **Tests**: Promo validation, deprecated calculate_discount, CRUD operations
- **Focus**: Validates that promo codes work as labels only

#### Transaction Calculation Tests
- **URL**: `http://localhost:8080/test_unit/detailed_transaction`
- **Tests**: Item discounts, transaction totals, edge cases
- **Focus**: Validates that discounts come from item level, not promo level

#### Quick Basic Tests
- **URL**: `http://localhost:8080/test_unit`
- **Purpose**: Basic CodeIgniter Unit Test functionality

## 🎯 What the Tests Validate

### ✅ Promo Refactoring Validation

| Test Category | What It Checks | Expected Result |
|---------------|----------------|-----------------|
| **Promo Validation** | `validate_promo()` method works | Returns promo info without discount calculation |
| **Deprecated Method** | `calculate_discount()` returns 0 | Always returns 0 discount regardless of amount |
| **Integration** | Promo codes in transactions | Promo appears in transaction but doesn't affect totals |

### ✅ Transaction Calculation Validation

| Test Category | What It Checks | Expected Result |
|---------------|----------------|-----------------|
| **Item Discounts** | Individual item discount calculation | `(qty × price) - item_discount` |
| **Multiple Items** | Complex transactions | Correct sum of all item subtotals |
| **PPN Calculation** | Tax calculation | `total × 10%` |
| **Grand Total** | Final amount | `total + ppn` (no promo discount) |

### ✅ Edge Cases Validation

| Test Category | What It Checks | Expected Result |
|---------------|----------------|-----------------|
| **Large Discounts** | Discounts > item total | Handled gracefully |
| **Zero Quantity** | Items with 0 qty | Calculated correctly |
| **Invalid Data** | Non-existent products/promos | Proper error handling |

## 📊 Understanding Test Results

### Success Output Example
```
Test Name: Promo validation should succeed
Expected: TRUE
Actual: TRUE
Result: PASS ✅
```

### Failure Output Example
```
Test Name: Calculate discount should return 0
Expected: 0
Actual: 50000
Result: FAIL ❌
```

### Summary Interpretation
```
Summary: 45 passed, 0 failed, 45 total
✅ All tests passed! 🎉

OR

Summary: 43 passed, 2 failed, 45 total
❌ 2 TESTS FAILED
```

## 🔧 Test Environment Setup

### Prerequisites
1. CodeIgniter 3 application running
2. Database with test data
3. Web server (Apache/Nginx)
4. PHP 5.6+ (recommended 7.4+)

### Test Data Requirements
The tests automatically create minimal test data:
- **Promo**: `DISC10` (Test Discount 10%)
- **Products**: `BRG001`, `BRG002` (Test products)

## 🚨 Troubleshooting

### Common Issues and Solutions

#### 1. "Test_unit controller not found"
**Problem**: URL returns 404
**Solution**: 
```bash
# Check if file exists
ls application/controllers/Test_unit.php

# Verify URL format
http://localhost:8080/test_unit
```

#### 2. "Database error" or "Model not found"
**Problem**: Missing models or database issues
**Solution**:
```bash
# Check models exist
ls application/models/Promo_model.php
ls application/models/Penjualan_model.php

# Check database connection in application/config/database.php
```

#### 3. "Function not found" errors
**Problem**: Test files not loading
**Solution**:
```bash
# Check test files exist
ls tests/promo_tests.php
ls tests/penjualan_tests.php

# Verify file paths in Test_unit.php constructor
```

#### 4. Tests fail with "Promo not found"
**Problem**: Test data not created
**Solution**: Visit cleanup and recreate test data:
```
http://localhost:8080/test_unit/cleanup
```

## 📈 Interpreting Results

### 🎯 Key Success Metrics

For the refactored promo functionality to be working correctly, you should see:

1. **Promo validation tests pass** ✅
   - `validate_promo` returns success for valid promos
   - `validate_promo` returns error for invalid promos

2. **Deprecated method tests pass** ✅
   - `calculate_discount` always returns 0
   - Method still exists for backward compatibility

3. **Transaction calculation tests pass** ✅
   - Grand total = subtotal + PPN (no promo discount)
   - Item discounts calculated correctly
   - Promo info present but doesn't affect amounts

4. **Integration tests pass** ✅
   - Transactions with promos work
   - Promo codes stored as labels
   - No discount calculation in transaction flow

### ❌ Failure Scenarios

If tests fail, check:

1. **Promo discount not zero** - Check if old discount logic is still active
2. **Transaction totals wrong** - Verify PPN calculation and item discount logic
3. **Promo validation fails** - Check database connection and test data
4. **Integration errors** - Verify controller and view updates

## 🎉 Success Confirmation

When all tests pass, you'll see:

```
🎉 ALL TESTS PASSED! 🎉

The refactored promo functionality is working correctly:
✅ Promo codes work as labels only
✅ No discount calculation in promo logic  
✅ Item discounts handled properly
✅ Transaction calculations are correct
```

This confirms that:
- ✅ Promo refactoring completed successfully
- ✅ Business logic working as intended  
- ✅ No regressions introduced
- ✅ All edge cases handled properly

## 📞 Getting Help

If you encounter issues:

1. **Check the logs**: Look at CodeIgniter error logs
2. **Verify environment**: Ensure all prerequisites are met
3. **Review test output**: Look for specific error messages
4. **Check file permissions**: Ensure web server can read files
5. **Database connectivity**: Verify database connection works