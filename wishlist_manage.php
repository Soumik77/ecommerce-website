<?php
require_once __DIR__ . '/connection.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_csrf();
$user = current_user();
if (!$user) { echo 'not_login'; exit; }
$pid = positive_int($_POST['pid'] ?? null);
if (!get_product($con, '', '', $pid)) throw new InvalidArgumentException('Product not available.');
wishlist_add($con, $user['id'], $pid);
echo db_one('SELECT COUNT(*) AS count FROM wishlist WHERE user_id = ?', [(int) $user['id']])['count'];
