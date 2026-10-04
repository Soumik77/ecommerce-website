<?php
require_once 'connection.inc.php';
require_once dirname(__DIR__) . '/includes/orders.php';
require_admin();
$id = positive_int($_GET['id'] ?? null);
$order = db_one('SELECT o.*, s.name AS status_name FROM `order` o JOIN order_status s ON s.id=o.order_status WHERE o.id=?', [$id]);
if (!$order) { http_response_code(404); exit('Order not found.'); }
$message = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    try {
        change_order_status($id, positive_int($_POST['order_status'] ?? null), isset($_POST['cash_received']));
        redirect_to('admin/order_master_detail.php?id=' . $id);
    } catch (InvalidArgumentException $error) { $message = $error->getMessage(); }
}
$lines = order_lines($id);
$next = [1 => [2,4], 2 => [3,4], 3 => [5], 4 => [], 5 => []][(int) $order['order_status']];
$statuses = db_query('SELECT * FROM order_status ORDER BY id')->fetch_all(MYSQLI_ASSOC);
require('top.inc.php');
?>
<main class="content"><div class="card"><div class="card-body"><h1>Order <?php echo $id; ?></h1>
<p><?php echo h($order['status_name']); ?> | Payment: <?php echo h($order['payment_status']); ?></p>
<p><?php echo h($order['address']); ?>, <?php echo h($order['city']); ?>, <?php echo h($order['pincode']); ?></p>
<table class="table"><thead><tr><th>Product</th><th>Quantity</th><th>Unit price</th><th>Amount</th></tr></thead><tbody>
<?php foreach ($lines as $line): ?><tr><td><?php echo h($line['name']); ?></td><td><?php echo h($line['qty']); ?></td><td><?php echo h($line['price']); ?></td><td><?php echo h(money_string(money_cents($line['price']) * $line['qty'])); ?></td></tr><?php endforeach; ?></tbody></table>
<p>Total: <?php echo h($order['total_price']); ?></p><p role="alert"><?php echo h($message); ?></p>
<?php if ($next): ?><form method="post"><?php echo csrf_field(); ?><label>Next status <select name="order_status" required><?php foreach ($statuses as $status): if (in_array((int) $status['id'], $next, true)): ?><option value="<?php echo (int) $status['id']; ?>"><?php echo h($status['name']); ?></option><?php endif; endforeach; ?></select></label>
<?php if (in_array(5, $next, true)): ?><label><input type="checkbox" name="cash_received" value="1" required> Cash payment received</label><?php endif; ?>
<button class="btn btn-primary" type="submit">Update status</button></form><?php endif; ?>
</div></div></main><?php require('footer.inc.php'); ?>
