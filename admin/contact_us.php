<?php
$definition = json_decode("{\"page\": \"contact_us.php\", \"table\": \"contact_us\", \"title\": \"Contact messages\", \"query\": \"SELECT * FROM contact_us ORDER BY id DESC\", \"columns\": {\"id\": \"ID\", \"name\": \"Name\", \"email\": \"Email\", \"comment\": \"Message\", \"added_on\": \"Date\"}, \"edit\": \"\"}", true);
require dirname(__DIR__) . '/includes/admin_listing.php';
