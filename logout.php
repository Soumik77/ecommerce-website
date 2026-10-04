<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_csrf();
unset($_SESSION['USER_LOGIN'], $_SESSION['USER_ID'], $_SESSION['USER_NAME'], $_SESSION['cart']);
session_regenerate_id(true);
redirect_to('index.php');
