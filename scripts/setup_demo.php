<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/includes/bootstrap.php';
$connection = db();
if ($connection->query('SHOW TABLES')->num_rows > 0) {
    fwrite(STDERR, "Setup refused: use a new, empty database. Existing data will not be overwritten.\n"); exit(1);
}
$connection->multi_query(file_get_contents(dirname(__DIR__) . '/database/ecom.sql'));
do {
    if ($result = $connection->store_result()) $result->free();
    if (!$connection->more_results()) break;
} while ($connection->next_result());

// These publicly documented accounts are only for the local synthetic demo.
$password = 'PortfolioDemo!2026';
$connection->begin_transaction();
try {
    db_query('INSERT INTO admin_users (id, username, password) VALUES (1, ?, ?)', ['demo_admin', password_hash($password, PASSWORD_DEFAULT)]);
    foreach ([1 => 'Customer One', 2 => 'Customer Two'] as $id => $name) {
        db_query('INSERT INTO users (id, name, email, mobile, password, added_on) VALUES (?, ?, ?, ?, ?, NOW())', [$id, $name, $id === 1 ? 'customer.one@example.test' : 'customer.two@example.test', '+1000000000' . $id, password_hash($password, PASSWORD_DEFAULT)]);
    }
    db_query('INSERT INTO categories (id, categories) VALUES (1, ?), (2, ?)', ['Clothing', 'Accessories']);
    $products = [
        [1, 1, 'Demo Shirt', '24.99', '19.99', 20, 'demo-shirt.svg'],
        [2, 2, 'Demo Bag', '45.00', '39.50', 15, 'demo-bag.svg'],
        [3, 2, 'Demo Shoes', '70.00', '60.00', 10, 'demo-shoes.svg'],
    ];
    foreach ($products as $product) {
        db_query('INSERT INTO product (id,categories_id,name,mrp,price,qty,image,short_desc,description,meta_title,meta_desc,meta_keyword,best_seller) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)', array_merge($product, ['Synthetic demonstration product.', 'This item is sample data for a local portfolio application.', $product[2], 'Synthetic catalog item', 'demo']));
    }
    db_query('INSERT INTO `order` (id,user_id,address,city,pincode,payment_type,total_price,payment_status,order_status,added_on) VALUES (1001,1,?,?,?, ?,?, ?,1,?), (1002,2,?,?,?, ?,?, ?,2,?)', ['1 Example Road','Demo City','00001','COD','39.98','pending','2026-10-01 10:00:00','2 Example Road','Demo City','00002','COD','39.50','pending','2026-10-02 10:00:00']);
    db_query('INSERT INTO order_detail (order_id,product_id,qty,price) VALUES (1001,1,2,?), (1002,2,1,?)', ['19.99','39.50']);
    $connection->commit();
    echo "Created the nine-table schema and synthetic demonstration records. See README.md for local demo accounts.\n";
} catch (Throwable $error) {
    $connection->rollback(); throw $error;
}
