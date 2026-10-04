<?php
$definition = json_decode("{\"page\": \"categories.php\", \"table\": \"categories\", \"title\": \"Categories\", \"query\": \"SELECT * FROM categories ORDER BY categories\", \"columns\": {\"id\": \"ID\", \"categories\": \"Category\", \"status\": \"Active\"}, \"edit\": \"manage_categories.php\"}", true);
require dirname(__DIR__) . '/includes/admin_listing.php';
