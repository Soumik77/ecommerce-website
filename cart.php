<?php
require_once 'connection.inc.php';
require_once 'functions.inc.php';
$items = [];
foreach ($_SESSION['cart'] ?? [] as $id => $entry) {
    $product = db_one('SELECT p.*, c.status AS category_status FROM product p JOIN categories c ON c.id = p.categories_id WHERE p.id = ?', [(int) $id]);
    $items[] = ['id' => (int) $id, 'product' => $product, 'qty' => (int) $entry['qty']];
}
require('top.php');
$total = 0;
?>
<main class="container" style="padding:50px 20px"><h1>Shopping cart</h1>
<?php if (!$items): ?><p>Your cart is empty.</p><?php else: ?>
<table class="table"><thead><tr><th>Product</th><th>Quantity</th><th>Amount</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($items as $item): $p = $item['product']; $amount = $p ? money_cents($p['price']) * $item['qty'] : 0; $total += $amount; ?>
<tr><td><?php echo h($p['name'] ?? 'Unavailable product'); ?><?php if (!$p || !$p['status'] || !$p['category_status']): ?> (unavailable)<?php endif; ?></td>
<td><input type="number" min="1" max="999" id="<?php echo $item['id']; ?>qty" value="<?php echo $item['qty']; ?>" aria-label="Quantity"></td>
<td><?php echo h(money_string($amount)); ?></td><td><button type="button" onclick="manage_cart(<?php echo $item['id']; ?>,'update')">Update</button> <button type="button" onclick="manage_cart(<?php echo $item['id']; ?>,'remove')">Remove</button></td></tr>
<?php endforeach; ?></tbody><tfoot><tr><th colspan="2">Total</th><td><?php echo h(money_string($total)); ?></td><td></td></tr></tfoot></table>
<p><a class="btn btn-primary" href="checkout.php">Checkout</a></p>
<?php endif; ?><p><a href="index.php">Continue shopping</a></p></main>
<?php require('footer.php'); ?>
