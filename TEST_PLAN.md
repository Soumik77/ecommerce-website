# Verification plan

## Local checks without MySQL

`php tests/run.php` checks exact currency conversion, invalid quantities/IDs, malformed text fields, HTML escaping, and session form-token generation. PHP syntax checks and `node --check js/custom.js` supplement these checks.

## Database checks

Run `php scripts/setup_demo.php` in a new database ending in `_test`, then run `ECOM_ALLOW_INTEGRATION_TESTS=1 php tests/integration.php` with the same connection settings. Use a disposable database; the integration suite creates orders and a temporary trigger.

The checks cover:

- A customer's access to their own order and rejection of another customer's order.
- Search text treated as a value and correct product sorting.
- Seeded password hashes verified through the PHP password API.
- Exact order totals, pending COD payment, and stock deductions.
- Empty/invalid product quantities and insufficient or missing stock.
- Cancellation restoring stock once.
- An injected failure on the second order item rolling back the order header, earlier items, and stock changes.

The temporary trigger is removed in a `finally` block. If the process is forcibly terminated, recreate the disposable test database before rerunning.

## HTTP checks

Start `php -S 127.0.0.1:8000 router.php` with the test database configured, then run `ECOM_ALLOW_INTEGRATION_TESTS=1 python3 tests/http_smoke.py` in another terminal with the same environment.

The script uses separate cookie jars for two customers and an administrator. It checks visible page responses, session behavior, form-token enforcement, order ownership, output escaping, protected files, disabled email/OTP endpoints, registration, and checkout.

## Manual review

After automated checks pass, review the storefront in a browser, including product images, sorting, cart updates, and checkout. Sign in as both demo customers to check order history. Check administrator category/product forms and image uploads. Confirm that no real personal data, local configuration, or generated uploads are included in the commit.

The included checks are focused regression coverage, not a complete production-security or visual-browser audit. The demo has no payment gateway, email verification, or account-recovery service.
