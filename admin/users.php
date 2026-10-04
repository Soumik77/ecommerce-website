<?php
$definition = json_decode("{\"page\": \"users.php\", \"table\": \"users\", \"title\": \"Customer accounts\", \"query\": \"SELECT id, name, email, mobile, added_on, status FROM users ORDER BY id DESC\", \"columns\": {\"id\": \"ID\", \"name\": \"Name\", \"email\": \"Email\", \"mobile\": \"Phone\", \"added_on\": \"Registered\", \"status\": \"Active\"}, \"edit\": \"\"}", true);
require dirname(__DIR__) . '/includes/admin_listing.php';
