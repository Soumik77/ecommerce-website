<?php
require_once 'connection.inc.php'; require_admin();
$orders = db_query('SELECT o.*, s.name AS status_name, u.name AS customer FROM `order` o JOIN order_status s ON s.id=o.order_status JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetch_all(MYSQLI_ASSOC);
require('top.inc.php'); ?>
<main class="content"><div class="card"><div class="card-body"><h1>Orders</h1><table class="table"><thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th></tr></thead><tbody>
<?php foreach ($orders as $order): ?><tr><td><a href="order_master_detail.php?id=<?php echo (int) $order['id']; ?>"><?php echo (int) $order['id']; ?></a></td><td><?php echo h($order['customer']); ?></td><td><?php echo h($order['added_on']); ?></td><td><?php echo h($order['total_price']); ?></td><td><?php echo h($order['status_name']); ?></td><td><?php echo h($order['payment_status']); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></main><?php require('footer.inc.php'); ?>
