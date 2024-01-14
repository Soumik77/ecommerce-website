<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ecom";

// Create connection
session_start();
$con = mysqli_connect($host, $user, $password, $database);
define('SERVER_PATH',$_SERVER['DOCUMENT_ROOT'].'/php/ecom/');
define('SITE_PATH','http://127.0.0.1/php/ecom/');
define('PRODUCT_IMAGE_SERVER_PATH',SERVER_PATH.'admin/upload/');
define('PRODUCT_IMAGE_SITE_PATH',SITE_PATH.'admin/upload/');



// ... Rest of your code ...
?>

