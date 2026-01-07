#  Task B

This project implements **Task B: Laravel Package for User Discounts**. It features a reusable, PSR-4 compliant package with deterministic stacking and a full automated test suite.

## 🚀 Package: `acme/user-discounts`

### 1. Key Functionality
- **Assign & Revoke**: Idempotent methods to grant or remove discounts for specific users.
- **Eligibility Engine**: Real-time validation of discounts based on:
  - **Active Status**: Inactive discounts are skipped.
  - **Expiration**: Expired discounts are automatically ignored.
  - **Usage Caps**: Enforces per-user usage limits (e.g., "One-time use only").
- **Deterministic Stacking**: Applies multiple discounts in a specific order (e.g., apply percentage first, then fixed amount).
- **Rounding Strategy**: Supports `up`, `down`, or `nearest` rounding for final prices.

### 2. Monitoring & Auditing
- **Event Driven**: Fires `DiscountAssigned`, `DiscountRevoked`, and `DiscountApplied` events.
- **Audit Logs**: Every action is recorded in the `discount_audits` table with metadata (price before/after).
- **Concurrency Safety**: Uses database-level locking (`lockForUpdate`) to prevent double-incrementing usage during simultaneous requests.

---

## 🛠️ Usage Example

```php
use Acme\UserDiscounts\Models\Discount;

// 1. Assign to user
$user->assignDiscount('WELCOME10');

// 2. Calculate final price
$finalPrice = $user->applyDiscounts(100.00); 
```

---

## 🧪 Testing

### Automated Tests
The package includes both **Unit** and **Feature** tests.

```bash
# Run Unit Tests (Logic & Caps)
php artisan test tests/Unit/DiscountUsageTest.php

# Run Feature Tests (Full Flow)
php artisan test tests/Feature/DiscountPkgTest.php
```

### GitHub CI/CD
Just like Task A, this repository is configured with GitHub Actions to automatically verify these tests on every push.

---

## 📦 Installation (Internal)
The package is located in `packages/acme/user-discounts` and is linked via the main `composer.json` using a path repository.
```json
"repositories": [
    {
        "type": "path",
        "url": "packages/acme/user-discounts"
    }
]
```
