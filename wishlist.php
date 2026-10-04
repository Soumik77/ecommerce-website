<?php
require_once 'connection.inc.php';
$user = require_user();
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    db_query('DELETE FROM wishlist WHERE id = ? AND user_id = ?', [positive_int($_POST['id'] ?? null), (int) $user['id']]);
    redirect_to('wishlist.php');
}
$items = db_query('SELECT w.id, p.id AS product_id, p.name, p.price FROM wishlist w JOIN product p ON p.id = w.product_id WHERE w.user_id = ?', [(int) $user['id']])->fetch_all(MYSQLI_ASSOC);
require('top.php');
?>
<main class="container" style="padding:50px 20px"><h1>Wishlist</h1>
<?php if (!$items): ?><p>Your wishlist is empty.</p><?php endif; ?>
<table class="table"><thead><tr><th>Product</th><th>Price</th><th>Action</th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr><td><a href="product.php?id=<?php echo (int) $item['product_id']; ?>"><?php echo h($item['name']); ?></a></td><td><?php echo h($item['price']); ?></td><td><?php echo post_button('remove', (int) $item['id'], 'Remove'); ?></td></tr><?php endforeach; ?>
</tbody></table></main><?php require('footer.php'); ?>
