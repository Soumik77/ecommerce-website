<?php
require_once __DIR__ . '/bootstrap.php';

function customer_order(int $orderId, int $userId): ?array
{
    return db_one('SELECT o.*, s.name AS order_status_str FROM `order` o JOIN order_status s ON s.id = o.order_status WHERE o.id = ? AND o.user_id = ?', [$orderId, $userId]);
}
function order_lines(int $orderId): array
{
    return db_query('SELECT d.*, p.name, p.image FROM order_detail d JOIN product p ON p.id = d.product_id WHERE d.order_id = ? ORDER BY d.id', [$orderId])->fetch_all(MYSQLI_ASSOC);
}
function place_order(int $userId, array $cart, array $address): int
{
    if (!$cart || count($cart) > 100) throw new InvalidArgumentException('The cart must contain between 1 and 100 products.');
    foreach (['address' => 250, 'city' => 50, 'pincode' => 20] as $field => $limit) {
        $address[$field] = input_string($address, $field, $limit);
        if ($address[$field] === '') throw new InvalidArgumentException('Complete the delivery address.');
    }
    $quantities = [];
    foreach ($cart as $id => $entry) $quantities[positive_int($id)] = positive_int($entry['qty'] ?? null, 'Quantity', 999);
    ksort($quantities);
    $connection = db();
    $connection->begin_transaction();
    try {
        if (!db_one('SELECT id FROM users WHERE id = ? AND status = 1', [$userId])) throw new InvalidArgumentException('Sign in with an active account.');
        $slots = implode(',', array_fill(0, count($quantities), '?'));
        $products = db_query('SELECT p.*, c.status AS category_status FROM product p JOIN categories c ON c.id = p.categories_id WHERE p.id IN (' . $slots . ') ORDER BY p.id FOR UPDATE', array_keys($quantities))->fetch_all(MYSQLI_ASSOC);
        if (count($products) !== count($quantities)) throw new InvalidArgumentException('A product in the cart no longer exists.');
        $total = 0;
        foreach ($products as $product) {
            $quantity = $quantities[$product['id']];
            if (!$product['status'] || !$product['category_status'] || $quantity > $product['qty']) throw new InvalidArgumentException('An item is unavailable or does not have enough stock.');
            $total += money_cents($product['price']) * $quantity;
        }
        if ($total > 99999999999) throw new InvalidArgumentException('The order value is too large.');
        db_query('INSERT INTO `order` (user_id, address, city, pincode, payment_type, total_price, payment_status, order_status, added_on) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())', [$userId, $address['address'], $address['city'], $address['pincode'], 'COD', money_string($total), 'pending']);
        $orderId = (int) db_one('SELECT LAST_INSERT_ID() AS id')['id'];
        foreach ($products as $product) {
            $quantity = $quantities[$product['id']];
            db_query('INSERT INTO order_detail (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)', [$orderId, (int) $product['id'], $quantity, $product['price']]);
            $changed = db_query('UPDATE product SET qty = qty - ? WHERE id = ? AND qty >= ?', [$quantity, (int) $product['id'], $quantity]);
            if ($changed !== 1) throw new RuntimeException('Stock update failed.');
        }
        $connection->commit();
        return $orderId;
    } catch (Throwable $error) {
        $connection->rollback(); throw $error;
    }
}
function change_order_status(int $orderId, int $next, bool $cashReceived): void
{
    $allowed = [1 => [2, 4], 2 => [3, 4], 3 => [5], 4 => [], 5 => []];
    $connection = db();
    $connection->begin_transaction();
    try {
        $order = db_one('SELECT * FROM `order` WHERE id = ? FOR UPDATE', [$orderId]);
        if (!$order || !in_array($next, $allowed[(int) $order['order_status']] ?? [], true)) throw new InvalidArgumentException('That order-status transition is not allowed.');
        if ($next === 5 && !$cashReceived) throw new InvalidArgumentException('Confirm receipt of the cash payment before marking this COD order delivered.');
        if ($next === 4) {
            foreach (db_query('SELECT product_id, qty FROM order_detail WHERE order_id = ? ORDER BY product_id', [$orderId])->fetch_all(MYSQLI_ASSOC) as $line) db_query('UPDATE product SET qty = qty + ? WHERE id = ?', [(int) $line['qty'], (int) $line['product_id']]);
        }
        $payment = $next === 5 ? 'paid' : ($next === 4 ? 'cancelled' : 'pending');
        db_query('UPDATE `order` SET order_status = ?, payment_status = ? WHERE id = ?', [$next, $payment, $orderId]);
        $connection->commit();
    } catch (Throwable $error) {
        $connection->rollback(); throw $error;
    }
}
