<?php
require_once __DIR__ . '/connection.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once __DIR__ . '/add_to_cart.inc.php';
require_csrf();
$pid = positive_int($_POST['pid'] ?? null);
$type = input_string($_POST, 'type', 20);
$cart = new add_to_cart();
if ($type === 'remove') {
    $cart->removeProduct($pid);
} elseif (in_array($type, ['add', 'update'], true)) {
    $qty = positive_int($_POST['qty'] ?? 1, 'Quantity', 999);
    $products = get_product($con, '', '', $pid);
    if (!$products || $products[0]['qty'] < $qty) throw new InvalidArgumentException('The requested quantity is not available.');
    if ($type === 'add' && !isset($_SESSION['cart'][$pid]) && $cart->totalProduct() >= 100) throw new InvalidArgumentException('The cart can hold at most 100 different products.');
    if ($type === 'add') $cart->addProduct($pid, $qty); else $cart->updateProduct($pid, $qty);
} else {
    throw new InvalidArgumentException('Invalid cart action.');
}
echo $cart->totalProduct();
