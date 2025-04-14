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
    short_description VARCHAR(255),
    long_description TEXT,
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
INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Apple MacBook Pro 16-inch (M3 Pro)',
    'Powerful laptop for creatives with stunning display and performance.',
    'The Apple MacBook Pro 16-inch with the M3 Pro chip is a powerhouse for professionals in creative industries. 
Its Liquid Retina XDR display delivers incredible color accuracy and brightness, making it perfect for video editing, design, and development. 
With the new M3 Pro architecture, users experience blazing-fast speeds, superior battery life, and outstanding thermal performance.

It includes a wide range of ports including HDMI, SD card slot, and Thunderbolt 4, ensuring compatibility with professional workflows. 
Combined with a studio-quality mic system and a six-speaker sound system, the MacBook Pro offers an all-in-one creative workstation experience.',
    3299.99,
    'Apple-MacBook-Pro-16-inch-(M3-Pro).jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Samsung Galaxy S24 Ultra',
    'Flagship Android phone with AI camera and advanced display tech.',
    'The Samsung Galaxy S24 Ultra redefines mobile photography with its 200MP main sensor and AI-assisted enhancements. 
Its large 6.8-inch Dynamic AMOLED display offers vibrant colors and a smooth 120Hz refresh rate, ideal for media consumption and gaming. 
Equipped with the latest Snapdragon processor and massive RAM options, it handles multitasking effortlessly.

With an integrated S Pen, users can take notes, draw, and navigate with precision. Its robust build, water resistance, and advanced camera features make it a top-tier choice for Android enthusiasts and professionals alike.',
    1399.99,
    'samsungS24.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Sony WH-1000XM5 Headphones',
    'Industry-leading noise cancellation with premium sound quality.',
    'Sony’s WH-1000XM5 headphones bring a new level of audio fidelity and noise cancellation to the market. 
With improved drivers and eight microphones, they deliver clear sound and exceptional call quality, even in noisy environments. 
These headphones support LDAC, Hi-Res Audio, and have intuitive touch controls for easy playback and volume adjustment.

Comfortable for long hours, they feature soft synthetic leather and a lightweight frame. With up to 30 hours of battery life and quick charge capabilities, these are perfect for travelers, remote workers, and audiophiles alike.',
    399.99,
    'Sony-WH-1000XM5-Headphones.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Dell XPS 13 Plus',
    'Sleek ultrabook with edge-to-edge display and great performance.',
    'The Dell XPS 13 Plus is a futuristic take on the classic ultrabook. 
It features a near-borderless 13.4-inch OLED display and a capacitive touch function row, blending aesthetics with function. 
Powered by Intel’s 13th-gen processors and fast NVMe SSDs, it delivers smooth multitasking and fast boot times.

Its minimalist design and glass touchpad make it stand out in a crowded field of laptops. Whether you''re coding, writing, or browsing, the XPS 13 Plus is a refined companion for modern users who value both form and function.',
    1249.99,
    'Dell-XPS-13-Plus.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Apple iPad Pro 12.9-inch (M2)',
    'Powerful tablet with desktop-level performance and ProMotion display.',
    'The iPad Pro 12.9-inch with M2 chip blurs the line between tablet and laptop. 
Its mini-LED Liquid Retina XDR display supports ProMotion for ultra-smooth visuals and HDR content playback. 
With support for the Apple Pencil 2 and Magic Keyboard, it''s ideal for artists, students, and professionals on the go.

It boasts Wi-Fi 6E, 5G capabilities, and all-day battery life. Combined with iPadOS’s powerful multitasking tools, this iPad offers an experience close to a full-fledged computer in a much lighter, more portable form.',
    1099.99,
    'Apple-iPad-Pro-12.9-inch-(M2).jpg',
    CURRENT_TIMESTAMP
);


-- more

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Google Pixel 8 Pro',
    'Premium Android phone with advanced AI features and clean OS.',
    'The Google Pixel 8 Pro offers the best of Android with Google’s pure vision and powerful AI tools. 
Its Tensor G3 chip enhances voice recognition, real-time translation, and photography with computational power. 
The 6.7-inch OLED display provides vibrant visuals, and the triple-camera setup captures detailed images in any lighting.

With long-term software support and exclusive Pixel features, it’s a top choice for those who prefer stock Android. 
Security updates, Material You design, and smooth integration with Google services make the Pixel 8 Pro a productivity powerhouse.',
    999.99,
    'Google-Pixel-8-Pro.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Lenovo Yoga 9i',
    '2-in-1 convertible laptop with premium design and audio.',
    'The Lenovo Yoga 9i is a stylish 2-in-1 laptop designed for productivity and media consumption. 
With a 14-inch touchscreen and 360-degree hinge, it transforms seamlessly from laptop to tablet. 
Powered by Intel Core i7 and Iris Xe graphics, it handles everyday tasks with ease.

The rotating soundbar with Bowers & Wilkins speakers provides immersive audio, while its all-metal chassis gives a premium feel. 
Ideal for students and creatives who value versatility and design.',
    1399.0,
    'Lenovo-Yoga-9i.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Apple Watch Series 9',
    'Advanced smartwatch with health tracking and Siri integration.',
    'The Apple Watch Series 9 offers enhanced performance, a brighter display, and innovative features like Double Tap gestures. 
Its health sensors monitor heart rate, blood oxygen, and sleep, making it a reliable health companion.

Seamlessly integrated with the Apple ecosystem, it allows quick responses, app interactions, and fitness tracking on the go. 
Its durable build and stylish bands make it both functional and fashionable.',
    399.0,
    'Apple-Watch-Series-9.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'ASUS ROG Zephyrus G14',
    'Compact gaming laptop with powerful specs and sleek design.',
    'The ASUS ROG Zephyrus G14 packs impressive gaming performance into a compact and portable chassis. 
Equipped with AMD Ryzen 9 and NVIDIA RTX 4070, it handles demanding games and creative tasks smoothly.

Its QHD 165Hz display ensures fluid visuals, and the AniMe Matrix lid adds a customizable aesthetic touch. 
Perfect for gamers and creators who want power without bulk.',
    1899.99,
    'ASUS-ROG-Zephyrus-G14.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Canon EOS R7 Mirrorless Camera',
    'High-speed mirrorless camera with 32.5MP sensor and 4K video.',
    'The Canon EOS R7 is a mirrorless camera tailored for enthusiasts and professionals alike. 
It features a 32.5MP APS-C sensor, Dual Pixel autofocus, and in-body stabilization, ensuring sharp images and smooth 4K video.

Its ergonomic build and weather sealing make it ideal for outdoor shoots. 
With fast burst rates and accurate tracking, it''s a top choice for wildlife and action photography.',
    1499.0,
    'Canon-EOS-R7-Mirrorless-Camera.jpg',
    CURRENT_TIMESTAMP
);


-- more

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Samsung Galaxy Tab S9 Ultra',
    'High-end Android tablet with stunning AMOLED display and S Pen.',
    'The Galaxy Tab S9 Ultra is Samsung’s most powerful tablet yet, featuring a 14.6-inch AMOLED display and the Snapdragon 8 Gen 2 processor. With the included S Pen and DeX support, it’s ideal for multitasking, creativity, and productivity.

Its premium build, quad speakers, and water resistance make it a versatile device for entertainment and work alike. Perfect for those who want a tablet that can replace a laptop.',
    1199.00,
    'galaxyTabS9.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Microsoft Surface Laptop Studio 2',
    'Versatile creative laptop with 2-in-1 design and powerful GPU.',
    'The Surface Laptop Studio 2 is a flexible device for creatives and developers. Its dynamic woven hinge allows easy switching between laptop, stage, and studio modes. Equipped with NVIDIA RTX graphics and high-refresh display, it handles graphics-heavy tasks with ease.

It supports pen input and touch, making it a great tool for designers and architects. Perfect for Windows users needing performance and adaptability.',
    2199.00,
    'microsoftSurfaceLS2.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Logitech MX Master 3S',
    'Ergonomic wireless mouse with customizable controls.',
    'The Logitech MX Master 3S is a productivity-focused mouse with a comfortable ergonomic design. Its quiet click buttons, high DPI precision, and ultra-fast scrolling make it ideal for long work sessions.

With customizable gestures and app-specific profiles, it streamlines multitasking across multiple screens. Great for developers, editors, and professionals.',
    99.99,
    'LogitechMXMaster3S.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'Anker 737 Power Bank',
    'Fast-charging power bank with 140W output and digital display.',
    'The Anker 737 Power Bank delivers high-capacity charging with a 24,000mAh battery and up to 140W output. It can charge laptops, phones, and tablets simultaneously, with a smart digital display that shows real-time data.

Its compact build and multiple ports make it a must-have for travelers and power users. Keep your devices running wherever you go.',
    149.95,
    'Anker737PowerBank.jpg',
    CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_description, long_description, price, photo, created_at) VALUES (
    'DJI Mini 4 Pro Drone',
    'Lightweight drone with 4K video and advanced flight features.',
    'The DJI Mini 4 Pro is a compact drone designed for easy flying and high-quality aerial footage. Weighing under 249g, it avoids registration requirements in many countries while offering features like obstacle avoidance and subject tracking.

It captures stunning 4K video and crisp 48MP photos, making it ideal for hobbyists and content creators. Foldable and portable, it’s ready to go on any adventure.',
    759.00,
    'DJI-Mini-4-Pro-Drone.jpg',
    CURRENT_TIMESTAMP
);
