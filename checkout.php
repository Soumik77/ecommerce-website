<?php
require_once 'connection.inc.php';
require_once 'functions.inc.php';
require_once 'includes/orders.php';
$user = require_user();
if (empty($_SESSION['cart'])) redirect_to('cart.php');
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    try {
        if (($_POST['payment_type'] ?? '') !== 'COD') throw new InvalidArgumentException('Only cash on delivery is available in this demonstration.');
        $orderId = place_order((int) $user['id'], $_SESSION['cart'], $_POST);
        unset($_SESSION['cart']);
        $_SESSION['last_order_id'] = $orderId;
        redirect_to('thank_you.php');
    } catch (InvalidArgumentException $failure) {
        $error = $failure->getMessage();
    } catch (Throwable $failure) {
        error_log('Checkout failed: ' . get_class($failure));
        $error = 'Your order could not be saved. Your cart is unchanged. Please try again.';
    }
}
$items = [];
try { $items = cart_items($_SESSION['cart']); } catch (InvalidArgumentException $failure) { $error = $failure->getMessage(); }
$total = array_sum(array_column($items, 'line_cents'));
require('top.php');
?>
<main class="container" style="padding:50px 20px">
  <h1>Checkout</h1>
  <p>Demonstration orders only. No payment is collected.</p>
  <?php if ($error): ?><p class="alert alert-danger" role="alert"><?php echo h($error); ?></p><?php endif; ?>
  <div class="row"><div class="col-md-7">
    <form method="post">
      <?php echo csrf_field(); ?>
      <p><label>Street address <input class="form-control" name="address" maxlength="250" required value="<?php echo h($_POST['address'] ?? ''); ?>"></label></p>
      <p><label>City <input class="form-control" name="city" maxlength="50" required value="<?php echo h($_POST['city'] ?? ''); ?>"></label></p>
      <p><label>Postal code <input class="form-control" name="pincode" maxlength="20" required value="<?php echo h($_POST['pincode'] ?? ''); ?>"></label></p>
      <p><label><input type="radio" name="payment_type" value="COD" checked required> Cash on delivery</label></p>
      <button class="btn btn-primary" type="submit" <?php echo $items ? '' : 'disabled'; ?>>Place demo order</button>
      <a href="cart.php">Return to cart</a>
    </form>
  </div><div class="col-md-5">
    <h2>Order summary</h2><table class="table"><thead><tr><th>Product</th><th>Quantity</th><th>Amount</th></tr></thead><tbody>
    <?php foreach ($items as $item): ?><tr><td><?php echo h($item['name']); ?></td><td><?php echo h($item['cart_qty']); ?></td><td><?php echo h(money_string($item['line_cents'])); ?></td></tr><?php endforeach; ?>
    </tbody><tfoot><tr><th colspan="2">Total</th><td><?php echo h(money_string($total)); ?></td></tr></tfoot></table>
  </div></div>
</main>
<?php require('footer.php'); ?>
