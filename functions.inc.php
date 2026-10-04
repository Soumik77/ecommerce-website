<?php
require_once __DIR__ . '/includes/bootstrap.php';

function get_safe_value($con, $value): string
{
    // Compatibility for page inputs. SQL values must still be bound parameters.
    return is_scalar($value) ? trim((string) $value) : '';
}
function get_product($con, $limit = '', $cat_id = '', $product_id = '', $search_str = '', $sort_order = '', $is_best_seller = ''): array
{
    $sql = 'SELECT p.*, c.categories FROM product p JOIN categories c ON c.id = p.categories_id WHERE p.status = 1 AND c.status = 1';
    $parameters = [];
    foreach (['p.categories_id' => $cat_id, 'p.id' => $product_id] as $column => $value) {
        if ($value !== '') {
            $sql .= ' AND ' . $column . ' = ?';
            $parameters[] = positive_int($value);
        }
    }
    if ($is_best_seller !== '') $sql .= ' AND p.best_seller = 1';
    if ($search_str !== '') {
        $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
        $parameters[] = '%' . $search_str . '%';
        $parameters[] = '%' . $search_str . '%';
    }
    $sorts = [
        'order by product.price desc' => 'p.price DESC, p.id DESC',
        'order by product.price asc' => 'p.price ASC, p.id DESC',
        'order by product.id asc' => 'p.id ASC',
        'order by product.id desc' => 'p.id DESC',
    ];
    $sql .= ' ORDER BY ' . ($sorts[trim($sort_order)] ?? 'p.id DESC');
    if ($limit !== '') {
        $sql .= ' LIMIT ?';
        $parameters[] = positive_int($limit, 'Limit', 1000);
    }
    return db_query($sql, $parameters)->fetch_all(MYSQLI_ASSOC);
}
function wishlist_add($con, $uid, $pid): void
{
    db_query('INSERT INTO wishlist (user_id, product_id, added_on) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE product_id = VALUES(product_id)', [(int) $uid, positive_int($pid)]);
}
function cart_items(array $cart): array
{
    $items = [];
    foreach ($cart as $id => $entry) {
        $products = get_product(db(), '', '', positive_int($id));
        if (!$products) throw new InvalidArgumentException('A product in the cart is no longer available. Remove it before checkout.');
        $product = $products[0];
        $product['cart_qty'] = positive_int($entry['qty'] ?? null, 'Quantity', 999);
        $product['line_cents'] = money_cents($product['price']) * $product['cart_qty'];
        $items[] = $product;
    }
    return $items;
}
