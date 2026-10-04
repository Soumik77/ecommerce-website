<?php
require_once 'connection.inc.php';
$user = require_user();
$orders = db_query('SELECT o.*, s.name AS order_status_str FROM `order` o JOIN order_status s ON s.id = o.order_status WHERE o.user_id = ? ORDER BY o.id DESC', [(int) $user['id']])->fetch_all(MYSQLI_ASSOC);
require('top.php');
?>
<main class="container" style="padding:50px 20px"><h1>My orders</h1>
<?php if (!$orders): ?><p>No orders yet.</p><?php else: ?>
<table class="table"><thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th></tr></thead><tbody>
<?php foreach ($orders as $order): ?><tr>
<td><a href="my_order_details.php?id=<?php echo (int) $order['id']; ?>"><?php echo (int) $order['id']; ?></a></td>
<td><?php echo h($order['added_on']); ?></td><td><?php echo h($order['total_price']); ?></td><td><?php echo h($order['order_status_str']); ?></td><td><?php echo h($order['payment_status']); ?></td>
</tr><?php endforeach; ?></tbody></table><?php endif; ?></main>
<?php require('footer.php'); ?>
