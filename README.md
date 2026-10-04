# E-commerce Catalog and Order Management

A PHP and MySQL portfolio application for browsing a product catalog, maintaining a shopping cart, and managing demonstration orders. The storefront uses the existing HTML, CSS, and JavaScript theme from this repository.

## Features

- Product categories, search, and price/date sorting.
- Customer registration and sign-in with hashed passwords.
- Session-based cart, stock checks, and customer wishlists.
- Cash-on-delivery demonstration checkout with exact decimal amounts.
- Customer order history with ownership checks before displaying details.
- Administrator pages for products, categories, customer accounts, messages, and order status.
- Order headers, items, and stock changes saved in a single database transaction.

This is a local demonstration. It does not collect payments, send email or SMS, or verify ownership of an email address. Password recovery and OTP endpoints are disabled. Use synthetic information when exploring the application.

## Requirements

- PHP 8.3 with `mysqli`, `mysqlnd`, and `fileinfo` available.
- MySQL 8.0.16 or newer, using InnoDB tables.
- Python 3 only if you want to run the optional HTTP regression checks.

No JavaScript build step or Composer installation is required for this version.

## Local setup

1. Clone this repository and enter its root directory:

   ```sh
   git clone https://github.com/Soumik77/ecommerce-website.git
   cd ecommerce-website
   ```

2. In your local MySQL installation, create a **new, empty** database named `ecommerce_portfolio`. Use a local database account that can create tables and read/write this database. For example, run these statements in a MySQL administrative session, choosing your own local password:

   ```sql
   CREATE DATABASE ecommerce_portfolio CHARACTER SET utf8mb4;
   CREATE USER 'ecommerce_user'@'localhost' IDENTIFIED BY 'choose-your-local-password';
   GRANT ALL PRIVILEGES ON ecommerce_portfolio.* TO 'ecommerce_user'@'localhost';
   ```

3. Copy the configuration example:

   ```sh
   cp config.example.php config.local.php
   ```

   Edit `config.local.php` to match your database host, port, account, and password. Use `DB_SOCKET` only when connecting through a local MySQL Unix socket. The default application URL is `http://127.0.0.1:8000/`. Environment variables with the same names override the local configuration.

4. Initialize the schema and synthetic records:

   ```sh
   php scripts/setup_demo.php
   ```

   Setup refuses to run if the selected database already contains tables. It does not upgrade or erase an old database. Do not import a previous personal-data dump into this demo.

5. Start the local server with the supplied router:

   ```sh
   php -S 127.0.0.1:8000 router.php
   ```

   Open `http://127.0.0.1:8000/`. The router blocks direct web access to configuration, database files, internal helpers, and test scripts. Keep `router.php` in the command.

## Public demo accounts

The setup script generates password hashes for these synthetic local accounts. Their shared demonstration password is **`PortfolioDemo!2026`**.

| Role | Sign-in page | Username or email |
| --- | --- | --- |
| Administrator | `/admin/login.php` | `demo_admin` |
| Customer One | `/login.php` | `customer.one@example.test` |
| Customer Two | `/login.php` | `customer.two@example.test` |

These credentials are public test fixtures. They must not be reused for a real account or a public production deployment. The database contains three synthetic products and two example orders belonging to different customers.

## Data and order handling

- Request values use prepared SQL statements. Sort choices use a fixed allowlist.
- Passwords are hashed with PHP's password API. Password inputs are accepted at 12 to 72 bytes for new accounts.
- Forms and AJAX mutations require a session form token; sign-in regenerates the session ID.
- User-supplied text is escaped when rendered into HTML.
- Product prices and stored order amounts use `DECIMAL`; application calculations use integer cents.
- Checkout locks the relevant stock rows and rolls back the whole order if any item cannot be saved.
- Orders progress from Pending to Processing to Shipped to Delivered. Pending or Processing orders can be cancelled, restoring stock once. Delivery requires an explicit confirmation that the COD payment was received.
- Products, categories, and customers can be deactivated without deleting records referenced by orders.
- Uploaded product images are limited to validated JPEG/PNG files, use generated filenames, and are ignored by Git. The three supplied SVG images are synthetic fixtures.

The nine tables are `admin_users`, `users`, `categories`, `product`, `order_status`, `order`, `order_detail`, `wishlist`, and `contact_us`. Foreign keys preserve relationships; unique keys prevent duplicate account emails and wishlist entries.

## Verification

Run the checks that do not need a database:

```sh
php tests/run.php
```

The suite includes MySQL checks for order ownership, checkout, stock, cancellation, and rollback after an injected database failure. It also includes HTTP checks for sign-in, registration, form tokens, private-file access, and customer/admin pages.

For these checks, create a separate empty database whose name ends in `_test`, configure the test connection through environment variables, and run:

```sh
php scripts/setup_demo.php
ECOM_ALLOW_INTEGRATION_TESTS=1 php tests/integration.php
```

Run the HTTP checks against the local server with the same test database:

```sh
ECOM_ALLOW_INTEGRATION_TESTS=1 python3 tests/http_smoke.py
```

The HTTP suite creates test records and assumes a fresh synthetic setup. The provided GitHub Actions workflow creates an isolated MySQL service, prepares the database, and runs all three groups of checks on pushes and pull requests. Check the **Actions** tab for actual results; the existence of a workflow does not indicate that it passed.

## Repository map

| Path | Purpose |
| --- | --- |
| Root PHP pages | Storefront and customer actions |
| `admin/` | Administrator pages and existing theme assets |
| `includes/bootstrap.php` | Configuration, sessions, prepared queries, and validation |
| `includes/orders.php` | Ownership checks and transactional order operations |
| `database/ecom.sql` | Schema without personal account/order records |
| `scripts/setup_demo.php` | Empty-database setup and synthetic seed records |
| `tests/` | Validation, database, and HTTP checks |
| `config.example.php` | Shareable configuration example |
| `.github/workflows/php.yml` | Automated verification configuration |

The existing front-end theme and vendor assets retain their original notices. Database reporting and Excel/Google Sheets dashboards are possible future extensions and are not implemented in this version.
