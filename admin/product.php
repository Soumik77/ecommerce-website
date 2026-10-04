<?php
$definition = json_decode("{\"page\": \"product.php\", \"table\": \"product\", \"title\": \"Products\", \"query\": \"SELECT p.*, c.categories FROM product p JOIN categories c ON c.id = p.categories_id ORDER BY p.id DESC\", \"columns\": {\"id\": \"ID\", \"categories\": \"Category\", \"name\": \"Product\", \"price\": \"Price\", \"qty\": \"Stock\", \"status\": \"Active\"}, \"edit\": \"manage_product.php\"}", true);
require dirname(__DIR__) . '/includes/admin_listing.php';
