<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_csrf();
unset($_SESSION['ADMIN_LOGIN'], $_SESSION['ADMIN_ID'], $_SESSION['ADMIN_USERNAME']);
session_regenerate_id(true);
redirect_to('admin/login.php');
