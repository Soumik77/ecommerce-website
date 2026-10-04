<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/functions.inc.php';
require_once dirname(__DIR__) . '/includes/orders.php';
if (getenv('ECOM_ALLOW_INTEGRATION_TESTS') !== '1' || !str_ends_with((string) app_config('DB_NAME'), '_test')) {
    fwrite(STDERR, "Refused: set ECOM_ALLOW_INTEGRATION_TESTS=1 and use a disposable database whose name ends in _test.\n"); exit(1);
}
require __DIR__ . '/run.php';
check(customer_order(1001, 1) !== null, 'Order owner can load their order');
check(customer_order(1001, 2) === null, 'Another customer cannot load that order');
check(customer_order(999999, 1) === null, 'Unknown order is absent');
check(get_product(db(), '', '', '', "' OR 1=1 --") === [], 'Search text stays a bound value');
$sorted = get_product(db(), '', '', '', '', ' order by product.price asc ');
check($sorted[0]['name'] === 'Demo Shirt', 'Price sorting produces the correct first product');
$user = db_one('SELECT password FROM users WHERE id=1');
check($user['password'] !== 'PortfolioDemo!2026' && password_verify('PortfolioDemo!2026', $user['password']), 'Seeded passwords are hashed and verify');
$stock = (int) db_one('SELECT qty FROM product WHERE id=1')['qty'];
$address = ['address'=>'3 Example Road','city'=>'Demo City','pincode'=>'00003'];
$id = place_order(1, [1=>['qty'=>2]], $address);
$saved = customer_order($id, 1);
check($saved['total_price'] === '39.98' && $saved['payment_status'] === 'pending', 'Checkout records exact total with pending COD payment');
check((int) db_one('SELECT qty FROM product WHERE id=1')['qty'] === $stock-2, 'Checkout reduces stock');
check(count(order_lines($id)) === 1, 'Checkout saves its line items');
change_order_status($id, 4, false);
check((int) db_one('SELECT qty FROM product WHERE id=1')['qty'] === $stock, 'Cancellation restores stock');
rejects(fn () => change_order_status($id, 4, false), 'Repeated cancellation cannot restore stock twice');
rejects(fn () => place_order(1, [], $address), 'Empty cart cannot create an order');
rejects(fn () => place_order(1, [1=>['qty'=>-1]], $address), 'Negative quantity cannot create an order');
rejects(fn () => place_order(1, [1=>['qty'=>999]], $address), 'Insufficient stock rejects checkout');
rejects(fn () => place_order(1, [999999=>['qty'=>1]], $address), 'Unknown product rejects checkout');
$ordersBefore = (int) db_one('SELECT COUNT(*) AS n FROM `order`')['n'];
$linesBefore = (int) db_one('SELECT COUNT(*) AS n FROM order_detail')['n'];
// Deliberately fail the second line to exercise rollback after an earlier write.
db()->query('DROP TRIGGER IF EXISTS portfolio_test_reject_item');
db()->query("CREATE TRIGGER portfolio_test_reject_item BEFORE INSERT ON order_detail FOR EACH ROW BEGIN IF NEW.product_id = 3 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Synthetic test failure'; END IF; END");
$failed = false;
try { place_order(1, [1=>['qty'=>1],3=>['qty'=>1]], $address); }
catch (mysqli_sql_exception $error) { $failed = true; }
finally { db()->query('DROP TRIGGER portfolio_test_reject_item'); }
check($failed, 'Injected second-line database failure reached checkout');
check((int) db_one('SELECT COUNT(*) AS n FROM `order`')['n'] === $ordersBefore, 'Failed checkout rolls back the order header');
check((int) db_one('SELECT COUNT(*) AS n FROM order_detail')['n'] === $linesBefore, 'Failed checkout rolls back earlier line items');
check((int) db_one('SELECT qty FROM product WHERE id=1')['qty'] === $stock, 'Failed checkout restores earlier stock changes');
echo "$passed total checks passed with MySQL.\n";
