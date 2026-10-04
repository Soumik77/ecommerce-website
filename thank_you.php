<?php
require_once 'connection.inc.php';
require_once 'includes/orders.php';
$user = require_user();
$id = (int) ($_SESSION['last_order_id'] ?? 0);
if (!$id || !customer_order($id, (int) $user['id'])) redirect_to('my_order.php');
require('top.php');
?>
<main class="container" style="padding:60px 20px"><h1>Your demo order has been saved</h1>
<p>Order <?php echo $id; ?> is awaiting processing. No payment has been collected.</p>
<a href="my_order_details.php?id=<?php echo $id; ?>">View order</a></main>
<?php require('footer.php'); ?>
