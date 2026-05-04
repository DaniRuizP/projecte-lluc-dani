-- Lluc Sánchez - 2n ASIX - Projecte Lluc/Dani

-- creació de la BBDD, definim que volem utilitzar UTF8 per mostrar els caràcters correctament a la web final i evitar problemes.
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_db;

-- crear taules necessàries
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE, password VARCHAR(255));

CREATE TABLE products (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), price DECIMAL(10,2), stock INT, image VARCHAR(500));

CREATE TABLE orders (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, product_id INT, status VARCHAR(50));

-- insert amb dades d'exemple
INSERT INTO products (name, price, stock, image) VALUES 
('Portàtil ASUS Gaming', 1200.00, 10, 'https://i.imgur.com/0HVuqLp.jpeg'), 
('Monitor Zowie 144hz', 250.00, 5, 'https://i.imgur.com/whFhDUH.png'), 
('Teclat Mecànic Logitech', 80.00, 20, 'https://i.imgur.com/Lp6QtC6.jpeg');
