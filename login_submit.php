<?php
require_once __DIR__ . '/connection.inc.php';
require_csrf();
$email = strtolower(input_string($_POST, 'email', 190));
$password = $_POST['password'] ?? '';
if (!is_string($password) || strlen($password) > 72 || str_contains($password, "\0")) { echo 'wrong'; exit; }
$user = db_one('SELECT id, name, password FROM users WHERE email = ? AND status = 1', [$email]);
if (!$user || !password_verify($password, $user['password'])) { echo 'wrong'; exit; }
session_regenerate_id(true);
$_SESSION['USER_LOGIN'] = 'yes';
$_SESSION['USER_ID'] = (int) $user['id'];
$_SESSION['USER_NAME'] = $user['name'];
echo 'valid';
