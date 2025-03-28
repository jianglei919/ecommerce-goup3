-- 创建数据库
CREATE DATABASE IF NOT EXISTS ecommerce_group3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_group3;

-- 用户表
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100) NOT NULL UNIQUE,
    is_admin BOOLEAN DEFAULT FALSE
);

-- 产品表
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    photo VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 购物车表
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 购物车项
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES carts(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 订单表
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 订单项
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE orders_payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    postal VARCHAR(20),
    payment VARCHAR(20),
    card VARCHAR(20),
    expiry VARCHAR(10),
    cvv VARCHAR(10),
    note TEXT,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 管理员测试账户 password:123456
INSERT INTO users (username, password, name, address, phone, email, is_admin)
VALUES ('admin', '$2y$10$kWQmEaIPVEQe0ChUPIM7Met7mL6aDxlJ6lpeS2RgEHd4tw909d.6u', 'Admin', '123 Admin St', '1234567890', 'admin@gmail.com', TRUE);

-- 普通用户测试账户
INSERT INTO users (username, password, name, address, phone, email)
VALUES ('test1', '$2y$10$kWQmEaIPVEQe0ChUPIM7Met7mL6aDxlJ6lpeS2RgEHd4tw909d.6u', 'test1', '456 User Ave', '9876543210', 'test1@gmail.com');

-- 测试产品数据
INSERT INTO products (name, description, price, photo) VALUES
('Laptop A', 'High performance laptop.', 899.99, 'laptop1.jpg'),
('Smartphone X', 'Latest smartphone model.', 699.99, 'phone1.jpg'),
('Wireless Headphones', 'Noise cancelling headphones.', 149.99, 'headphones.jpg'),
('Smartwatch Y', 'Fitness tracking smartwatch.', 199.99, 'smartwatch.jpg'),
('Bluetooth Speaker', 'Portable Bluetooth speaker.', 89.99, 'speaker.jpg');
