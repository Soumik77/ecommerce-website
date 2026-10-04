<?php
require_once __DIR__ . '/connection.inc.php';
require_csrf();
$name = input_string($_POST, 'name', 100);
$email = input_string($_POST, 'email', 190);
$mobile = input_string($_POST, 'mobile', 20);
$comment = input_string($_POST, 'message', 2000);
if ($name === '' || $comment === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Enter your name, a valid email address, and a message.');
db_query('INSERT INTO contact_us (name, email, mobile, comment, added_on) VALUES (?, ?, ?, ?, NOW())', [$name, $email, $mobile, $comment]);
echo 'Your demo message has been saved. No email was sent.';
