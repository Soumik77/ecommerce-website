<?php
require_once __DIR__ . '/connection.inc.php';
require_csrf();
$name = input_string($_POST, 'name', 100);
$email = strtolower(input_string($_POST, 'email', 190));
$mobile = input_string($_POST, 'mobile', 20);
$password = $_POST['password'] ?? '';
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[+0-9 ()-]{7,20}$/D', $mobile)) {
    http_response_code(422); exit('Enter a name, valid email, and phone number.');
}
if (!is_string($password) || strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
    http_response_code(422); exit('Use a password between 12 and 72 bytes long.');
}
try {
    db_query('INSERT INTO users (name, email, mobile, password, added_on) VALUES (?, ?, ?, ?, NOW())', [$name, $email, $mobile, password_hash($password, PASSWORD_DEFAULT)]);
    echo 'insert';
} catch (mysqli_sql_exception $error) {
    if ($error->getCode() === 1062) { echo 'email_present'; } else { throw $error; }
}
