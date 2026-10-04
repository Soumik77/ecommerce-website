<?php
require_once 'connection.inc.php';
require_once 'includes/orders.php';
$user = require_user();
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$order = $id ? customer_order($id, (int) $user['id']) : null;
if (!$order) { http_response_code(404); exit('Order not found.'); }
// Fetch line items only after ownership is established.
$lines = order_lines($id);
require('top.php');
?>
<main class="container" style="padding:50px 20px"><h1>Order <?php echo $id; ?></h1>
<p><?php echo h($order['order_status_str']); ?> | Payment: <?php echo h($order['payment_status']); ?></p>
<p><?php echo h($order['address']); ?>, <?php echo h($order['city']); ?>, <?php echo h($order['pincode']); ?></p>
<table class="table"><thead><tr><th>Product</th><th>Quantity</th><th>Unit price</th><th>Amount</th></tr></thead><tbody>
<?php foreach ($lines as $line): ?><tr><td><?php echo h($line['name']); ?></td><td><?php echo h($line['qty']); ?></td><td><?php echo h($line['price']); ?></td><td><?php echo h(money_string(money_cents($line['price']) * $line['qty'])); ?></td></tr><?php endforeach; ?>
</tbody><tfoot><tr><th colspan="3">Total</th><td><?php echo h($order['total_price']); ?></td></tr></tfoot></table>
<a href="my_order.php">All my orders</a></main>
<?php require('footer.php'); ?>
