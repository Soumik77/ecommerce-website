-- Schema only. scripts/setup_demo.php inserts synthetic records and hashed passwords.
-- Import into a NEW, EMPTY database with MySQL 8.0.16+.
CREATE TABLE admin_users (
 id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(100) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE users (
 id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE, mobile VARCHAR(20) NOT NULL,
 password VARCHAR(255) NOT NULL, status TINYINT NOT NULL DEFAULT 1,
 added_on DATETIME NOT NULL, CHECK (status IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE categories (
 id INT PRIMARY KEY AUTO_INCREMENT, categories VARCHAR(100) NOT NULL UNIQUE,
 status TINYINT NOT NULL DEFAULT 1, CHECK (status IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE product (
 id INT PRIMARY KEY AUTO_INCREMENT, categories_id INT NOT NULL,
 name VARCHAR(150) NOT NULL, mrp DECIMAL(9,2) NOT NULL, price DECIMAL(9,2) NOT NULL,
 qty INT NOT NULL DEFAULT 0, image VARCHAR(255) NOT NULL,
 short_desc VARCHAR(2100) NOT NULL, description TEXT NOT NULL,
 meta_title VARCHAR(255) NOT NULL, meta_desc VARCHAR(2000) NOT NULL, meta_keyword VARCHAR(2000) NOT NULL,
 status TINYINT NOT NULL DEFAULT 1, best_seller TINYINT NOT NULL DEFAULT 0,
 FOREIGN KEY (categories_id) REFERENCES categories(id),
 CHECK (price >= 0), CHECK (mrp >= 0), CHECK (qty >= 0),
 CHECK (status IN (0,1)), CHECK (best_seller IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE order_status (id INT PRIMARY KEY, name VARCHAR(50) NOT NULL UNIQUE)
 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO order_status (id,name) VALUES (1,'Pending'),(2,'Processing'),(3,'Shipped'),(4,'Cancelled'),(5,'Delivered');
CREATE TABLE `order` (
 id INT PRIMARY KEY AUTO_INCREMENT, user_id INT NOT NULL,
 address VARCHAR(250) NOT NULL, city VARCHAR(50) NOT NULL, pincode VARCHAR(20) NOT NULL,
 payment_type VARCHAR(20) NOT NULL, total_price DECIMAL(12,2) NOT NULL,
 payment_status VARCHAR(20) NOT NULL DEFAULT 'pending', order_status INT NOT NULL DEFAULT 1,
 added_on DATETIME NOT NULL,
 FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (order_status) REFERENCES order_status(id),
 INDEX (added_on), CHECK (total_price >= 0),
 CHECK (payment_type = 'COD'), CHECK (payment_status IN ('pending','paid','cancelled'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE order_detail (
 id INT PRIMARY KEY AUTO_INCREMENT, order_id INT NOT NULL, product_id INT NOT NULL,
 qty INT NOT NULL, price DECIMAL(9,2) NOT NULL,
 FOREIGN KEY (order_id) REFERENCES `order`(id), FOREIGN KEY (product_id) REFERENCES product(id),
 UNIQUE KEY order_product (order_id,product_id), CHECK (qty > 0), CHECK (price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE wishlist (
 id INT PRIMARY KEY AUTO_INCREMENT, user_id INT NOT NULL, product_id INT NOT NULL, added_on DATETIME NOT NULL,
 FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (product_id) REFERENCES product(id),
 UNIQUE KEY user_product (user_id,product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE contact_us (
 id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL,
 mobile VARCHAR(20) NOT NULL, comment TEXT NOT NULL, added_on DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
