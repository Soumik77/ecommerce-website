<?php
class add_to_cart
{
    public function addProduct($pid, $qty): void { $_SESSION['cart'][$pid]['qty'] = $qty; }
    public function updateProduct($pid, $qty): void { if (isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid]['qty'] = $qty; }
    public function removeProduct($pid): void { unset($_SESSION['cart'][$pid]); }
    public function emptyProduct(): void { unset($_SESSION['cart']); }
    public function totalProduct(): int { return count($_SESSION['cart'] ?? []); }
}
